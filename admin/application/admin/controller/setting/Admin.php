<?php

/**
 * 用户类
 * jijianyong
 * 2020/9/7
 * 
 */

namespace app\admin\controller\setting;

use app\admin\library\Base;
use app\admin\model\system\Admin as AdminModel;
use Random;

class Admin extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];

    public function _initialize()
    {
        parent::_initialize();

        $this->model = new AdminModel;
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

        $list = $this->model->where($where)->field(['password', 'salt', 'token'], true)->page($page, $size)->select();

        foreach ($list as &$v) {
            $v['logintime'] = $v['logintime'] ? date('Y-m-d H:i', $v['logintime']) : '';
            $v['createtime'] = $v['createtime'] ? date('Y-m-d H:i', $v['createtime']) : '';
            $v['updatetime'] = $v['updatetime'] ? date('Y-m-d H:i', $v['updatetime']) : '';
        }

        json_return(0, '', ['total' => $total, 'list' => $list]);
    }

    // 增加用户
    public function add()
    {
        if (request()->isPost()) {

            $name = $this->request->param('name');
            $password = $this->request->param('password');
            $nickname = $this->request->param('nickname');
            $gids = $this->request->param();

            // 加盐
            $salt = Random::alnum();

            // 赋值
            $param = [
                'name' => $name,
                // 如果没有传入密码，则不加密，让下面的验证方法报错
                'password' => $password ? $this->model->encryptPassword($password, $salt) : '',
                'salt' => $salt,
                'nickname' => $nickname,
                'createtime' => time(),
            ];

            // 验证是否有存在用户名重复
            $isUsername = db('admin')->where(['name' => $param['name']])->find();
            if ($isUsername) {
                json_return(1, '登陆名已存在，请重新输入新的登录名！');
            }

            // 用户名与密码校验
            $result = $this->model->validate(
                [
                    'name'          => 'require|unique:admin',
                    'password'      => 'require'
                ],
                [
                    'name.require'  => 'name can not be empty',
                    'name.unique'   => 'name has been repeated',
                    'password.require' => 'password can not be empty',
                ]
            )->addAdmin($param);

            // 添加角色关联
            if ($result) {
                if (isset($gids)) {
                    if (is_array($gids['gid'])) { 
                        $this->model->accessGroup($result,$gids['gid']);
                    }else{
                        // 拼接字符串（用,号拼接角色id）
                        $gid = implode(',',$gids['gid']);
                        $this->model->accessGroup($result,$gid);
                    }
                }
            }

            // 完全等于false则报错
            if ($result === false)
                json_return(1, $this->model->getError());

            $this->log('添加管理员');
            json_return(0, 'add success');
        }
    }

    public function edit()
    {
        if (request()->isPost()) {

            $id = $this->request->param('id');
            $name = $this->request->param('name');
            $password = $this->request->param('password');
            $repassword = $this->request->param('password');
            $nickname = $this->request->param('nickname');

            $Admin = $this->model->get(['id' => $id]);

            if (!$Admin)
                json_return(1, 'admin info not found');

            // 加盐
            $salt = Random::alnum();

            $param = [
                'name' => $name,
                'nickname' => $nickname,
                'updatetime' => time(),
            ];

            if ($password) {

                if ($password !== $repassword)
                    json_return(1, 'please enter the same password again');

                // 加盐
                $param['salt'] = Random::alnum();

                $param['password'] = $this->model->encryptPassword($password, $param['salt']);
            }

            $result = $Admin->validate(
                [
                    'name'          => 'require|unique:admin,name,' . $Admin->id,
                ],
                [
                    'name.require'  => 'name can not be empty',
                    'name.unique'   => 'name has been repeated',
                ]
            )->save($param);

            // 完全等于false则报错
            if ($result === false)
                json_return(1, $Admin->getError());

            $this->log('编辑管理员');
            json_return(0, 'edit success');
        }
    }

    public function del()
    {

        if (request()->isPost()) {

            $id = $this->request->param('id');

            if ($id == 1)
                json_return(1, '不能删除超级管理员');

            // 删除用户数据以及用户关联角色表数据
            $result = $this->model->delAdmin($id);


            // 完全等于false则报错
            if ($result == false)
                json_return(1, $this->model->getError());

            $this->log('删除管理员');
            json_return(0, 'delete success');
        }
    }

    public function select()
    {
        $result = db('admin')->select();
        json_return(0, '', $result);
    }

    public function AssignSelect()
    {
        $result = db('admin')->field(['id','name','nickname'])->select();
        json_return(0, '', $result);
    }
}
