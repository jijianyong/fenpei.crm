<?php

/**
 * 权限规则类
 * jijianyong
 * 2020/9/9
 * 
 */

namespace app\admin\controller\setting;

use app\admin\library\Base;
use app\admin\model\system\Rule as ModelRule;


class Rule extends Base
{
    protected $noNeedLogin = [];

    protected $noNeedRight = ['*'];

    // 获取树节点数据
    public function index()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $model = new ModelRule();
        $list = $model->getRuleList([]);
        $tree = $model->getTreeList($list);
        json_return(0, '', $tree);
    }

    // 查询树节点数据
    public function select()
    {
        $model = new ModelRule();
        $list = $model->getRuleList();
        $tree = $model->getTreeItem($list);
        json_return(0, '', $tree);
    }

    // 新增节点
    public function add()
    {
        if (request()->isPost()) {
            $data = array(
                'pid' => input('param.pid', 0),
                'name' => input('param.name'),
                'title' => input('param.title'),
                'weigh' => input('param.weigh', 0),
                'condition' => input('param.condition', ''),
                'status' => input('param.status', 0),
                'ismenu' => input('param.ismenu', 0),
                'remark' => input('param.remark', ''),
                'createtime' => time(),
            );

            if (!$data['name'])
                json_return(1, '请输入规则');
            if (!$data['title'])
                json_return(1, '请输入标题');

            $model = new ModelRule();
            $result = $model->addRule($data);
            if ($result) {
                $this->log('新增[' . $data['title'] . ']权限规则');
                json_return(0, '添加成功');
            } else {
                json_return(1, '添加失败');
            }
        }
    }

    // 编辑节点数据
    public function edit()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        if (request()->isPost()) {

            $id = intval(request()->param('id', ''));
            if ($id <= 0) {
                json_return(1, '参数错误');
            }

            $data = array(
                'pid' => input('param.pid', 0),
                'name' => input('param.name'),
                'title' => input('param.title'),
                'weigh' => input('param.weigh'),
                'condition' => input('param.condition'),
                'status' => input('param.status'),
                'ismenu' => input('param.ismenu'),
                'remark' => input('param.remark'),
                'updatetime' => time(),
            );
            $model = new ModelRule();
            $result = $model->editRule($data, $id);
            if ($result) {
                $this->log('编辑[' . $data['title'] . ']权限规则');
                json_return(0, '修改成功');
            } else {
                json_return(1, '修改失败');
            }
        }
    }

    // 删除节点数据
    public function drop()
    {
        $rule_id = input('param.id');
        if (empty($rule_id)) {
            json_return(1, '参数错误');
        }
        $model = new ModelRule();
        $result = $model->delRule($rule_id);
        if ($result) {
            $this->log('删除权限规则');
            json_return(0, '删除成功');
        } else {
            json_return(1, '删除失败');
        }
    }

    // 批量删除节点数据
    public function list_del()
    {
        $rule_id = request()->param('id', '');
        $rule_id_array = ds_delete_param($rule_id);
        if ($rule_id_array == FALSE) {
            json_return(1, '参数错误');
        }
        $condition = array();
        $condition['id'] = array('in', $rule_id_array);

        $rule_model = new ModelRule();;
        if (!$rule_model->delRules($condition)) {
            json_return(1, '删除失败');
        } else {
            $this->log(lang('删除') . lang('权限规则'), 1);
            json_return(0, '删除成功');
        }
    }
}
