<?php

/**
 * 角色权限类
 * jijianyong
 * 2020/9/7
 * 
 */

namespace app\admin\controller\setting;

use app\admin\library\Base;
use app\admin\model\system\Group as ModelGroup;
use app\admin\model\system\Auth;

class Group extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['select', 'ruletree'];

    // 返回树形结构的权限数组
    public function index()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $model = new ModelGroup();
        // 步骤：1
        $list = $model->getMyGroupList();
        // 生成树形结构
        $tree = $model->getTreeList($list, min(array_column($list, 'pid'))); 
        json_return(0, '', $tree);
    }

    // 新增角色组
    public function add()
    {
        $model = new ModelGroup();
        $this->auth = new Auth();
        if (request()->isPost()) {
            $param = $this->request->param();
            $data['pid'] = isset($param['pid']) ? $param['pid'] : 0;
            $data['name'] = isset($param['name']) ? $param['name'] : '';
            // 获取子节点id
            $child_ids = $model->getMyChildIds();
            $a = $data['pid'];
            $b = $child_ids;
            $a = in_array($data['pid'], $child_ids);
            if (!in_array($data['pid'], $child_ids))
                json_return(1, '父级角色组不能是自己的子组');

            if (!$data['name'])
                json_return(1, '请输入角色组名称');

            if (isset($param['rule'])) {
                $rule_ids = $param['rule'];

                $parent_group = $model->getOneGroup(['id' => $data['pid']]);

                $parent_rules = explode(',', $parent_group['rules']);
                $current_rules = $this->auth->getRuleIds($this->auth->uid);

                // 过滤父级权限之外的节点
                $rule_ids = in_array('*', $parent_rules) ? $rule_ids : array_intersect($parent_rules, $rule_ids);
                // 过滤管理员没有权限的节点
                $rule_ids = in_array('*', $current_rules) ? $rule_ids : array_intersect($current_rules, $rule_ids);

                if ($rule_ids) {
                    $data['rules'] = implode(',', $rule_ids);
                }
            }
            $gid = $model->addGroup($data);
            if ($gid) {
                if (isset($param['field'])) {
                    $model->setGroupField($gid, json_encode(array_values($param['field'])));
                }
            }
            $this->log('新增[' . $data['name'] . ']管理员');
            json_return(0, '添加成功');
        }
    }

    // 编辑
    public function edit()
    {
        $model = new ModelGroup();
        $this->auth = new Auth();
        if (request()->isPost()) {
            $id = $this->request->param('id');
            $info = $model->getOneGroup(['id' => $id]);
            if (!$info)
                json_return(1, '无法获取该角色组信息');

            $param = $this->request->param();

            $data['pid'] = isset($param['pid']) ? $param['pid'] : 0;
            $data['name'] = isset($param['name']) ? $param['name'] : '';

            $child_ids = $model->getMyChildIds();

            if (!in_array($data['pid'], $child_ids))
                json_return(1, '父级角色组不能是自己的子组');

            if (!$data['name'])
                json_return(1, '请输入角色组名称');

            $rule_ids = [];
            if (isset($param['rule']) && $param['rule']) {
                $rule_ids = $param['rule'];

                $parent_group = $model->getOneGroup(['id' => $data['pid']]);

                $parent_rules = explode(',', $parent_group['rules']);
                $current_rules = $this->auth->getRuleIds($this->__admin['id']);

                // 过滤父级权限之外的节点
                $rule_ids = in_array('*', $parent_rules) ? $rule_ids : array_intersect($parent_rules, $rule_ids);
                // 过滤管理员没有权限的节点
                $rule_ids = in_array('*', $current_rules) ? $rule_ids : array_intersect($current_rules, $rule_ids);

                if ($rule_ids)
                    $data['rules'] = implode(',', $rule_ids);
            } else {
                $data['rules'] = '';
            }

            $model->editGroup($data, $id);

            if (isset($param['field'])) {
                $model->setGroupField($id, json_encode(array_values($param['field'])));
            } else {
                $model->delGroupField($id);
            }

            $this->log('新增[' . $data['name'] . ']管理员');
            json_return(0, '编辑成功');
        }
    }

    // 删除
    public function del()
    {
        $model = new ModelGroup();
        if ($this->request->isPost()) {
            $id = $this->request->param('id', '');
            $info = $model->getOneGroup(['id' => $id]);
            if (!$info)
                json_return(1, '无法获取该角色组信息');
            if ($model->getOneGroup(['pid' => $id]))
                json_return(1, '该角色组包含子级，不能删除');
            $model->delGroup($id);
            $this->log('删除[' . $info['name'] . ']角色组');
            json_return(0, '删除成功');
        }
    }

    // 角色组下拉列表
    public function select()
    {
        $model = new ModelGroup();
        $list = $model->getMyGroupList();
        $tree = $model->getTreeItem($list);
        json_return(0, '', $tree);
    }

    // 权限节点树，添加权限按钮调用的方法
    public function ruletree()
    {
        $pid = $this->request->param('pid', 0);
        $id = $this->request->param('id', 0);
        $model = new ModelGroup();
        $parent_group = $pid ? $model->getOneGroup(['id' => $pid]) : false;
        $current_group = $id ? $model->getOneGroup(['id' => $id]) : false;

        // 判断参数
        if (!$parent_group || ($id && !$current_group))
            json_return(1, '无法获取父级角色组信息');

        // 父级不能是自己的子级
        if ($id && in_array($pid, $model->getChildIds($id)))
            json_return(1, '无法将父级更改为子级');

        $rule_list = $model->getAllRuleList();

        $parent_rule_list = [];
        $parent_rule_ids = explode(',', $parent_group['rules']);
        if (in_array('*', $parent_rule_ids)) {
            $parent_rule_list = $rule_list;
        } else {
            foreach ($rule_list as $k => $v) {
                if (in_array($v['id'], $parent_rule_ids)) {
                    $parent_rule_list[] = $v;
                }
            }
        }

        $selected = [];
        if ($current_group)
            $selected = explode(',', $current_group['rules']);

        $rule_list_tree = $model->getRuleTree($parent_rule_list, 0, $selected);

        json_return(0, '', ['rule' => $rule_list_tree, 'selected' => $selected]);
    }
}
