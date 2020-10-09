<?php

/**
 * 菜单规则类
 * jijianyong
 * 2020/9/8
 * 
 */

namespace app\admin\controller\setting;

use app\admin\library\Base;
use app\admin\model\system\Menu as ModelMenu;

class Menu extends Base
{

    protected $noNeedLogin = [];

    protected $noNeedRight = ['select'];


    // 初始化查询树结构
    public function index()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $model = new ModelMenu();
        $list = $model->getList();
        $tree = $model->getTreeList($list);
        json_return(0, '', $tree);
    }

    // 查询父菜单下的所有列表
    public function select()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $model = new ModelMenu();
        $list = $model->getList(['pid' => 0]);
        json_return(0, '', $list);
    }

    // 添加菜单规则
    public function add()
    {
        if (request()->isPost()) {
            $data = array(
                'rule_id' => input('param.rule_id', 0),
                'pid' => input('param.pid', 0),
                'name' => input('param.name', ''),
                'path' => input('param.path') ? input('param.path') : '/',
                'icon' => input('param.icon', ''),
                'component' => input('param.component', ''),
                'hidden' => input('param.hidden') ? (int)input('param.hidden') : 0,
                'leaf' => input('param.leaf') ? (int)input('param.leaf') : 0,
                'weigh' => input('param.weigh') ? (int)input('param.weigh') : 0,
                'createtime' => time(),
            );
            $model = new ModelMenu();
            $result = $model->addMenu($data);
            if ($result) {
                $this->log('新增菜单规则');
                json_return(0, '添加成功');
            } else {
                json_return(1, '添加失败');
            }
        }
    }

    // 修改菜单规则
    public function edit()
    {
        if (request()->isPost()) {
            $id = intval(request()->param('id', ''));
            if ($id <= 0) {
                json_return(1, '参数错误');
            }

            $data = array(
                'rule_id' => input('param.rule_id', 0),
                'pid' => input('param.pid', 0),
                'name' => input('param.name', ''),
                'path' => input('param.path') ? input('param.path') : '/',
                'icon' => input('param.icon', ''),
                'component' => input('param.component', ''),
                'hidden' => input('param.hidden') ? (int)input('param.hidden') : 0,
                'leaf' => input('param.leaf') ? (int)input('param.leaf') : 0,
                'weigh' => input('param.weigh') ? (int)input('param.weigh') : 0,
                'updatetime' => time(),
            );
            $model = new ModelMenu();
            $result = $model->editMenu($data, $id);
            if ($result) {
                $this->log('编辑菜单规则成功');
                json_return(0, '修改成功');
            } else {
                json_return(1, '修改失败，请检查是否有缺少项');
            }
        }
    }

    // 删除菜单规则
    public function drop()
    {
        $rule_id = input('param.id');
        if (empty($rule_id)) {
            json_return(1, '参数错误,没能找到此条菜单规则');
        }
        $model = new ModelMenu();
        $result = $model->delMenu($rule_id);
        if ($result) {
            $this->log('删除菜单规则');
            json_return(0, '删除成功');
        } else {
            json_return(1, '删除失败');
        }
    }
}
