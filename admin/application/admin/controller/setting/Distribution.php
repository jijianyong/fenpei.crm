<?php

/**
 * 资源分配规则类
 * jijianyong
 * 2020/9/18
 * 
 */

namespace app\admin\controller\setting;

use app\admin\library\Base;
use app\admin\model\system\Distribution as DisModel;


class Distribution extends Base
{

    // 初始化
    public function _initialize()
    {
        parent::_initialize();

        $this->model = new DisModel;
    }


    public function index()
    {

        // 获取分页数据
        $page = $this->request->param('page') ?: 1;
        $size = $this->request->param('size') ?: 20;

        $filter = $this->request->param('filter');
        $filter = (array)json_decode($filter, true) ?: [];

        $where = [];
        if ($filter && !empty($filter['search']))
            $where['name|nickname'] = ['like', '%' . $filter['search'] . '%'];

        $total = $this->model->where($where)->count();

        // $list = $this->model->where($where)->field(['remark', 'createtime', 'updatetime'], true)->page($page, $size)->select();
        $rows = $this->model
                ->alias('d')
                ->join('tq_admin a','a.id=d.uid','left')
                ->where($where)
                ->field(['d.id','d.uid','a.name','d.start_time','d.end_time','d.express_address','d.express_source','d.express_advisory','d.nums','d.status','d.remark'])
                ->page($page, $size)
                ->select();

        json_return(0, '', ['total' => $total, 'list' => $rows]);
    }

    // 增加分配规则
    public function add()
    {
        if (request()->isPost()) {

            $params = $this->request->param();
            // 添上创建时间
            $params['createtime'] = time();
            unset($params['username']);
            unset($params['id']);

            // 添加防重复校验条件
            $where = [
                'uid' => $params['uid'],
                'start_time' => $params['start_time'],
                'end_time' => $params['end_time'],
                'express_address' => $params['express_address'],
                'express_source' => $params['express_source'],
                'express_advisory' => $params['express_advisory'],
            ];

            // 验证是否有存在重复的数据
            $isDistribution = db('auth_distribution')->where($where)->find();
            if ($isDistribution) {
                json_return(1, '这条分配规则已经存在了，请检查一下！');
            }

            // 添加分配规则
            $result = $this->model->addDistribution($params);

            // 完全等于false则报错
            if ($result === false)
                json_return(1, $this->model->getError());

            $this->log('添加资源分配规则');
            json_return(0, '恭喜，添加资源分配规则成功！');
        }
    }

    // 修改分配规则
    public function edit()
    {
        if (request()->isPost()) {

            $params = $this->request->param();
            unset($params['name']);
            // 添上修改时间
            $params['updatetime'] = time();

            // 修改分配规则
            $result = $this->model->editDistribution($params, $params['id']);
            
            // 完全等于false则报错
            if ($result === false)
                json_return(1, $this->model->getError());

            $this->log('编辑资源分配规则');
            json_return(0, '恭喜，修改资源分配规则成功！');
        }
    }

    // 删除分配规则
    public function del()
    {

        if (request()->isPost()) {

            $id = $this->request->param('id');

            // 删除用户数据以及用户关联角色表数据
            $result = $this->model->delDistribution($id);

            // 完全等于false则报错
            if ($result == false)
                json_return(1, $this->model->getError());

            $this->log('删除资源分配规则');
            json_return(0, '恭喜，删除资源分配规则成功！');
        }
    }
}
