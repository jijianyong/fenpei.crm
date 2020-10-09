<?php

/**
 * 菜单节点模型（菜单节点模型类）
 * jijianyong
 * 2020/9/9
 */

namespace app\admin\model\system;

use think\Model;

class Rule extends Model
{
    // 开启自动写入/更新时间戳
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public $page_info;


    /**
     * 获取列表
     * @access public
     * @author csdeshang
     * @param type $condition
     * @param type $page
     * @param type $order
     * @return type
     */
    public function getRuleList($condition = [], $page = '')
    {
        if ($page) {
            $result = db('auth_rule')
                ->where($condition)
                ->order('weigh DESC,name ASC,id ASC,pid ASC')
                ->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $result;
            return $result->items(); // 返回渲染分页
        } else {
            $result = db('auth_rule')->order('weigh DESC,name ASC,id ASC,pid ASC')->where($condition)->select();
            return $result;
        }
    }

    // 获取单条规则数据
    public function getRule($id)
    {
        $result = db('auth_rule')->find($id);
        return $result;
    }

    /**
     * 取单个内容
     * @access public
     * @author csdeshang
     * @param int $condition
     * @return array 数组类型的返回结果
     */
    public function getOneRule($condition)
    {
        $result = db('auth_rule')->where($condition)->find();
        return $result;
    }

    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addRule($data)
    {
        if (empty($data)) {
            return false;
        }
        $result = db('auth_rule')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editRule($data, $id)
    {
        $result = db('auth_rule')->where("id", $id)->update($data);
        return $result;
    }

    /**
     * 删除
     * @access public
     * @author csdeshang
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function delRule($id)
    {
        $result = db('auth_rule')->where("id", $id)->delete();
        return $result;
    }

    /**
     * 删除选择记录
     * @author csdeshang
     * @param type $condition 删除条件
     * @return type
     */
    public function delRules($condition)
    {
        $result = db('auth_rule')->where($condition)->delete();
        return $result;
    }

    /**
     * 获取包括自己和所有子级的ID
     * @param int $id  角色组ID
     * @return array
     */
    public function getChildIds($myid)
    {
        $newarr = [];
        $item = db('auth_rule')->field('id')->find($myid);
        if ($item) {
            $newarr[] = $item['id'];
            $child_db = db('auth_rule')->field('id')->where('pid', $myid)->select();
            foreach ($child_db as $v)
                $newarr = array_merge($newarr, $this->getChildIds($v['id']));
        }
        return $newarr;
    }

    /**
     * 根据$list，生成树形列表的item，必须配合 $this->getTreeList() 方法使用
     * @param array $list   角色组的列表
     * @param int   $pid    父级id
     * @param int   $level  当前等级
     * @return array
     */
    public function getTreeItem($list, $pid = 0, $level = 0)
    {
        $newarr = [];
        foreach ($list as $value) {
            if (!isset($value['id']))
                continue;
            if ($value['pid'] == $pid) {
                $value['spacer'] = $level > 0 ? str_repeat('|', $level) . '└' : '';
                $newarr[] = $value;
                $newarr = array_merge($newarr, $this->getTreeItem($list, $value['id'], $level + 1));
            }
        }
        return $newarr;
    }

    public function getTreeList($list, $pid = 0)
    {
        $newarr = [];
        foreach ($list as $value) {
            if (!isset($value['id']))
                continue;
            if ($value['pid'] == $pid) {
                $children = $this->getTreeList($list, $value['id']);
                if ($children)
                    $value['children'] = $children;
                $newarr[] = $value;
            }
        }
        return $newarr;
    }
}
