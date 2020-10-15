<?php

/**
 * 菜单模型（菜单模型类）
 * jijianyong
 * 2020/9/8
 */

namespace app\admin\model\system;

use think\Model;

class Menu extends Model
{
    // 开启自动写入/更新时间戳
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public $page_info;

    // 查询多条数据
    public function getList($condition = [])
    {
        $result = db('auth_menu')->order('weigh DESC,id ASC,pid ASC')->where($condition)->select();
        return $result;
    }

    // 查询一条数据
    public function getMenu($condition)
    {
        $result = db('auth_menu')->where($condition)->find();
        return $result;
    }

    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addMenu($data)
    {
        // 如果为空,则返回false
        if (empty($data)) {
            return false;
        }
        $result = db('auth_menu')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editMenu($data, $id)
    {
        $result =  db('auth_menu')->where("id", $id)->update($data);
        return $result;
    }

    /**
     * 删除
     * @access public
     * @author csdeshang
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function delMenu($id)
    {
        return db('auth_menu')->where("id", 'in', $id)->delete();
    }

    /**
     * 删除选择记录
     * @author csdeshang
     * @param type $condition 删除条件
     * @return type
     */
    public function delMeuns($condition)
    {
        return db('auth_menu')->where($condition)->delete();
    }

    /**
     * 根据$list，生成树形列表的item
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

    public function getMenuList($list, $pid = 0, $rule = [])
    {
        $newarr = [];
        foreach ($list as $value) {
            if (!isset($value['id']))
                continue;
            if ($value['pid'] == $pid) {
                // 过滤权限
                if ($value['rule_id'] > 0 && (!in_array('*', $rule) && !in_array($value['rule_id'], $rule)))
                    continue;
                $data = $this->formatMenu($value);
                $children = $this->getMenuList($list, $value['id'], $rule);
                if ($children)
                    $data['children'] = $children;
                $newarr[] = $data;
            }
        }
        return $newarr;
    }

    public function formatMenu($param)
    {
        return [
            'name' => $param['name'],
            'element' => isset($param['element']) ? $param['element'] : 0,
            'path' => $param['path'] ? $param['path'] : '/',
            'component' => $param['component'] ? $param['component'] : 'Home',
            'iconCls' => $param['icon'],
            'hidden' => $param['hidden'] ? true : false,
            'leaf' => $param['leaf'] ? true : false,
        ];
    }
}
