<?php

namespace app\admin\controller\sales;

use app\admin\library\Base;
use app\admin\model\sales\Order as OrderModel;
use ExcelToArrary;

class Order extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];

    public function _initialize()
    {
        parent::_initialize();

        $this->model = new OrderModel;
    }

    public function index()
    {

        // 获取分页数据
        $page = $this->request->param('page') ?: 1;
        $size = $this->request->param('size') ?: 20;
        $filter = $this->request->param('filter');

        if($filter){
            $where = ['express_advisory' => $filter];
        }else{
            $where = [];
        }
        
        $total = $this->model->where($where)->count();

        $list = $this->model
                ->alias('o')
                ->join('tq_admin a','a.id=o.remark','left')
                ->where($where)
                ->field(['a.name','o.express_no','o.express_name','o.express_moblie','o.express_address','o.express_source','o.express_advisory','o.express_adviseryurl','o.copy_num','o.id','o.createtime','o.updatetime'])
                ->limit(($page-1)*$size,$size)
                ->select();

        foreach ($list as &$v) {
            $v['createtime'] = $v['createtime'] ? date('Y-m-d H:i', $v['createtime']) : '';
            $v['updatetime'] = $v['updatetime'] ? date('Y-m-d H:i', $v['updatetime']) : '';
        }



        json_return(0, '', ['total' => $total, 'list' => $list]);
    }

    public function selUserList()
    {

        // 获取分页数据
        $page = $this->request->param('page') ?: 1;
        $size = $this->request->param('size') ?: 20;
        $user = $this->request->param('user');
        $user = db('admin')->where('name',$user)->value('id');

        $filter = $this->request->param('user');
        if($filter !== 'admin'){
            $where = ['remark' => $user];
        }else{
            $where = [];
        }

        $total = $this->model->where($where)->count();

        $list = $this->model->where($where)->limit(($page-1)*$size,$size)->select();

        foreach ($list as &$v) {
            $v['createtime'] = $v['createtime'] ? date('Y-m-d H:i', $v['createtime']) : '';
            $v['updatetime'] = $v['updatetime'] ? date('Y-m-d H:i', $v['updatetime']) : '';
        }



        json_return(0, '', ['total' => $total, 'list' => $list]);
    }

    // 新增资源
    public function add()
    {
        if (request()->isPost()) {

            $param = $this->request->param();

            if (!$param)
                json_return(1, '');


            $param['createtime'] = time();
            $time = date('Ymd', time());
            $num = $this->model->where('express_no', 'like', $param['username'] . $time . '%')->count();
            $num = $num + 1;
            $param['express_no'] = $param['username'] . $time . $num . '=' . $param['prefix_moblie'];
            $param['express_moblie'] = $param['prefix_moblie'] . '+' . $param['express_moblie'];
            unset($param['prefix_moblie']);
            unset($param['username']);
            $result = $this->model->validate(
                [
                    'express_no'              => 'require|unique:sales_order',
                    'express_name'          => 'require',
                    'express_moblie'        => 'require',
                    'express_address'       => 'require',
                    'express_source'          => 'require',
                    'express_advisory'      => 'require',
                    'express_adviseryurl'       => 'require',
                ],
                [
                    'express_no.require'              => 'order number can not be empty',
                    'express_no.unique'               => 'order number has been repeated',
                    'express_name.require'          => 'recipients can not be empty',
                    'express_moblie.require'        => 'recipients telephone can not be empty',
                    'express_address.require'       => 'consignee address can not be empty',
                    'express_source.require'                 => 'standard can not be empty',
                    'express_advisory.require'      => 'quantity can not be empty',
                    'express_adviseryurl.require'       => 'logistics company can not be empty',
                ]
            )->save($param);

            // 完全等于false则报错
            if ($result === false)
                json_return(1, $this->model->getError());

            $this->log('添加新资源');
            json_return(0, 'add success');
        }
    }

    // 修改资源信息
    public function edit()
    {
        if (request()->isPost()) {

            $id = $this->request->param('id');
            $param = $this->request->param();

            $Order = $this->model->get(['id' => $id]);

            if (!$Order)
                json_return(1, 'order info not found');

            $param['updatetime'] = time();

            $result = $Order->validate(
                [
                    'express_no'              => 'require|unique:sales_order,express_no,' . $Order->id,
                    'express_name'          => 'require',
                    'express_moblie'        => 'require',
                    'express_address'       => 'require',
                    'express_source'          => 'require',
                    'express_advisory'      => 'require',
                    'express_adviseryurl'       => 'require',
                ],
                [
                    'express_no.require'              => 'order number can not be empty',
                    'express_no.unique'               => 'order number has been repeated',
                    'express_name.require'          => 'recipients can not be empty',
                    'express_moblie.require'        => 'recipients telephone can not be empty',
                    'express_address.require'       => 'consignee address can not be empty',
                    'express_source.'                 => 'standard can not be empty',
                    'express_advisory.require'      => 'quantity can not be empty',
                    'express_adviseryurl.require'       => 'logistics company can not be empty',
                ]
            )->save($param);

            // 完全等于false则报错
            if ($result === false)
                json_return(1, $Order->getError());

            $this->log('编辑资源');
            json_return(0, 'edit success');
        }
    }

    // 删除资源信息
    public function del()
    {
        if (request()->isPost()) {

            $param = $this->request->param();
            $ids = $param['orderid'];
            if (!$ids)
                json_return(1, '');

            $result = $this->model->where('id', 'in', $ids)->delete();

            // 完全等于false则报错
            if ($result == false)
                json_return(1, $this->model->getError());

            $this->log('删除资源');
            json_return(0, 'delete success');
        }
    }

    // 复制资源信息
    public  function copys()
    {
        $id = $this->request->param('ids');
        $copy = $this->model->where('id', $id)->value('copy_num');
        $copy = $copy + 1;
        $ifcopy = $this->model->where('id', $id)->update(['copy_num' => $copy]);
        if ($ifcopy) {
            $this->log('复制资源');
            json_return(0, 'copy success');
        } else {
            json_return(1, 'order info not found');
        }
    }

    /**
     * 导入excel文件
     */
    public function imports()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        set_time_limit(0);
        ini_set("memory_limit", "5120M");
        $file = $this->request->file('file');

        if (empty($file)) {
            json_return(1, json('上传有问题！'));
        }

        $fileInfo = $file->getInfo();

        // 得到后缀
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        /*判别是不是.xls文件，判别是不是excel文件*/
        if ($suffix != "xlsx" && $suffix != "xls" && $suffix != "csv") {
            json_return(1, '只能导入Excel文件');
        }

        if ($fileInfo['size'] > 3145728)
            json_return(1, '导入文件不能大于3mb');

        // 处理表格，得到数组
        $ExcelToArrary = new ExcelToArrary(); //实例化
        $res = $ExcelToArrary->read($fileInfo['tmp_name'], "gb2312", $suffix);

        //导入数据顺序打乱也可以导入
        $header = $res[1];
        $where = [
            'express_name' => '客户姓名',
            'express_moblie' => '联系方式',
            'express_address' => '地址',
            'express_source' => '来源',
            'express_advisory' => '咨询项目',
            'express_adviseryurl' => '咨询链接',
        ];
        $list = [];
        foreach ($header as $k => &$v) {

            if ($v != '') {
                $datas =  array_search($v, $where);
                if ($datas != '') {
                    $list[$datas] = $k;
                } else {
                    json_return(1, '导入失败，表头[' . $v . ']不对，请核实');
                }
            }
        }
        $list_key = array_keys($list);

        $param['createtime'] = time();
        // 凡是导入进来的数据，默认都是admin角色导入的
        $username = 'tq';
        // 转换日期格式
        $time = date('Ymd', time());
        // 统计当前日期内新增有多少条数据
        $num = $this->model->where('express_no', 'like', $username . $time . '%')->count();
        // 获取下一位的数字
        $num = $num + 1;

        /*对生成的数组进行数据库的写入*/
        foreach ($res as $k => &$v) {
            if ($k > 1) {
                $demo = substr($v[1], 0, 1);
                $data[$k]['createtime'] = time();
                $data[$k]['express_no'] = $username . $time . $num . '=' . $demo;
                $num = $num + 1;
                foreach ($list_key as &$item) {
                    $data[$k][$item] = $v[$list[$item]];
                    if ($item == 'express_name') {
                        $data[$k]['express_name'] = $v[$list['express_name']];
                    }
                    if ($item == 'express_moblie') {
                        $data[$k]['express_moblie'] = $v[$list['express_moblie']];
                    }
                    if ($item == 'express_address') {
                        $data[$k]['express_address'] = $v[$list['express_address']];
                    }
                    if ($item == 'express_source') {
                        $data[$k]['express_source'] = $v[$list['express_source']];
                    }
                    if ($item == 'express_advisory') {
                        $data[$k]['express_advisory'] = $v[$list['express_advisory']];
                    }
                    if ($item == 'express_adviseryurl') {
                        $data[$k]['express_adviseryurl'] = $v[$list['express_adviseryurl']];
                    }
                }
            }
        }

        //插入的操作最好放在循环外面
        $result = db('sales_order')->insertAll($data, true);
        unset($data);
        if ($result) {
            $this->log('导入早报');
            json_return(0, '导入成功');
        } else {
            json_return(1, '导入失败');
        }
    }
}
