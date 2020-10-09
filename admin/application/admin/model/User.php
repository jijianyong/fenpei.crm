<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | 改自fastadmin 20190523 caca
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Db;
use app\admin\model\system\Auth;
use app\admin\model\system\Admin;

class User
{

    /**
     * @var object 对象实例
     */
    protected static $object;
 
    public $islogin = false;
    public $id = null;
    public $token = null;
    public $admin = false;

    /**
     * 实例化
     * @access public
     * @param int|string|array $options 参数
     * @return object
     */
    public static function instance($options = null)
    {
        if (is_null(self::$object))
        {
            self::$object = new static($options);
        }
        return self::$object;
    }

    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct($options)
    {
        $this->init($options);
    }

    /**
     * 初始化
     *
     * @param int|string|array $options 参数
     * @return boolean
     */
    public function init($options)
    {
        if(!$options)
            return false;
        else if( is_array($options) )
            $condition = $options;
        else if( is_numeric($options) )
            $condition = ['id'=>$options];
        else
            $condition = ['token'=>$options];

        if( empty($condition['token']) )
            return false;

        if($this->islogin)
            return true;

        $admin = Admin::get($condition);
        if (!$admin)
            return false;

        if ($admin->status != 0)
            return false;

        $this->auth = Auth::instance();
        
        $this->islogin = true;
        $this->id = $admin->id;
        $this->token = $admin->token;
        $this->admin = $admin;

        return true;
    }

    // 检查权限
    public function check($name, $relation = 'or')
    {
        return $this->auth->check( $name, $this->id, $relation);
    }

    // 获取用户组
    public function getGroups()
    {
        return $this->auth->getGroups($this->id);
    }

    // 获得权限规则列表
    public function getRuleList()
    {
        return $this->auth->getRuleList($this->id);
    }

    // 是否为超级管理员
    public function isSuperAdmin()
    {   
        return in_array('*', $this->auth->getRuleIds($this->id)) ? true : false;
    }

    // 是否登录
    public function isLogin()
    {
        return  $this->islogin ? true : false;
    }


    /**
     * 取出当前管理员所拥有权限的分组
     * @param boolean $withself 是否包含当前所在的分组
     * @return array
     */
    public function getChildrenGroupIds($withself = false)
    {
        //取出当前管理员所有的分组
        $groups = $this->getGroups();
        $groupIds = [];
        foreach ($groups as $k => $v) {
            $groupIds[] = $v['id'];
        }
        
        // 取出所有分组
        $groupList = \app\admin\model\system\Group::where(['status' => '0'])->select();
        $objList = [];
        foreach ($groups as $K => $v) {
            if ($v['rules'] === '*') {
                $objList = $groupList;
                break;
            }
        }
        
    }


}