<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | 改自fastadmin 20190523 caca
// +----------------------------------------------------------------------

namespace app\admin\model\system;

use think\Db;

/**
 * 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $sys=new Auth();  $sys->check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $sys=new Auth();  $sys->check('规则1,规则2','用户id','and')
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(think_auth_group_access表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(think_auth_group 定义了用户组权限)
 * 4，支持规则表达式。
 *      在think_auth_rule 表中定义一条规则，condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100
 * 表示用户的分数在5-100之间时这条规则才会通过。
 */
class Auth
{

    /**
     * @var object 对象实例
     */
    protected static $object;

    // 用户权限规则
    protected $rules = [];

    //默认配置
    protected $config = [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 1, // (暂时无用)
        'auth_group'        => 'auth_group', // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'auth_rule', // 权限规则表
    ];

    public $token = '';
    public $islogin = false;
    public $admin = false;
    public $uid = 1;


    /**
     * 初始化
     * @access public
     * @return Auth
     */
    public static function instance()
    {
        if (is_null(self::$object)) {
            self::$object = new static();
        }
        return self::$object;
    }

    /**
     * 根据Token初始化
     *
     * @param string       $token    Token
     * @return boolean
     */
    public function init($token)
    {
        if ($this->islogin)
            return true;

        if (!$token || $token == false)
            return false;

        $admin = Admin::get(['token' => $token]);
        if (!$admin)
            return false;

        if ($admin->status != 0)
            return false;

        $this->token = $token;
        $this->islogin = true;
        $this->admin = $admin;
        $this->uid = $admin->id;

        // 初始化成功的事件
        // Hook::listen("user_init_successed", $this->admin);

        return true;
    }

    public function init2($id)
    {
        $admin = Admin::get(['id' => $id]);
        if (!$admin)
            return false;

        if ($admin->status != 0)
            return false;

        $this->admin = $admin;
        $this->uid = $admin->id;

        // 初始化成功的事件
        // Hook::listen("user_init_successed", $this->admin);

        return true;
    }

    /**
     * 检查权限
     * @param       $name       string|array    需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param       $uid        int             用户id
     * @param       $relation   string          如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return bool               通过验证返回true;失败返回false
     */
    public function check($name, $relation = 'or')
    {
        // 是否开启了权限判断
        if (!$this->config['auth_on']) {
            return true;
        }

        // 没有登陆或者获取不到uid则不通过
        $uid = $this->uid;
        if (!$uid)
            return false;

        // 获取用户需要验证的所有有效规则列表
        $rulelist = $this->getRuleList($uid);

        if (in_array('*', $rulelist))
            return true;

        // 验证规则转换数组
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }

        $list = []; //保存验证通过的规则名

        // 验证
        foreach ($rulelist as $rule) {
            if (in_array($rule, $name)) {
                $list[] = $rule;
            }
        }

        // 判断规则
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param  $uid int     用户id
     * @return array       用户所属的用户组 array(
     *              array('uid'=>'用户id','group_id'=>'用户组id','name'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *              ...)
     */
    public function getGroups($uid)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }

        // 执行查询
        $user_groups = Db::name($this->config['auth_group_access'])
            ->alias('aga')
            ->join('__' . strtoupper($this->config['auth_group']) . '__ ag', 'aga.group_id = ag.id', 'LEFT')
            ->field('aga.uid,aga.group_id,ag.id,ag.pid,ag.name,ag.rules')
            ->where("aga.uid='{$uid}' and ag.status=0")
            ->select();
        $groups[$uid] = $user_groups ?: [];
        return $groups[$uid];
    }

    /**
     * 获得权限规则节点
     * @param integer $uid 用户id
     * @return array
     */
    public function getRuleIds($uid)
    {
        //读取用户所属用户组
        $demo = $uid; // 测试demo
        $groups = $this->getGroups($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ','))); //合并id
        }
        $ids = array_unique($ids); //去重
        return $ids;
    }

    /**
     * 获得权限规则列表
     * @param integer $uid 用户id
     * @return array
     */
    public function getRuleList($uid)
    {
        static $_rulelist = []; //保存用户验证通过的权限列表
        if (isset($_rulelist[$uid])) {
            return $_rulelist[$uid];
        }

        // 读取用户规则节点
        $ids = $this->getRuleIds($uid);
        if (empty($ids)) {
            $_rulelist[$uid] = [];
            return [];
        }

        // 筛选条件
        $where = [
            'status' => '0'
        ];
        if (!in_array('*', $ids)) // * 则返回全部
        {
            $where['id'] = ['in', $ids];
        }
        //读取用户组所有权限规则
        $this->rules = Db::name($this->config['auth_rule'])->where($where)->field('id,pid,condition,icon,name,title,ismenu')->select();

        //循环规则，判断结果。
        $rulelist = []; //
        if (in_array('*', $ids)) {
            $rulelist[] = "*";
        }
        foreach ($this->rules as $rule) {
            //超级管理员无需验证condition
            if (!empty($rule['condition']) && !in_array('*', $ids)) {
                // (暂无条件判断)
            } else {
                //只要存在就记录
                $rulelist[$rule['id']] = strtolower($rule['name']);
            }
        }
        $_rulelist[$uid] = $rulelist;
        //登录验证则需要保存规则列表
        return array_unique($rulelist);
    }
    public function isSuperAdmin()
    {
        $uid = $this->uid;
        return in_array('*', $this->getRuleIds($uid)) ? true : false;
    }

    public function isLogin()
    {
        return  $this->islogin ? true : false;
    }
}
