<?php

namespace app\admin\controller;

use think\Validate;
use app\admin\library\Base;
use app\admin\model\Login;
use app\admin\model\common\Captcha;
use app\admin\model\system\Auth;
use app\admin\model\system\Menu;
use think\Cookie;
use think\Session;

class Index extends Base
{

    protected $noNeedLogin = ['index', 'login'];
    protected $noNeedRight = ['logout', 'info', 'menu'];

    protected $Captcha = 1; // 是否开启验证码


    public function index()
    {
        echo 'ok';
    }

    // 获取权限内的菜单路径
    public function menu()
    {
        // 解决跨域问题
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');


        $menu = new Menu();
        $auth = new Auth();
        $list = $menu->getList();
        $rule_list = $auth->getRuleIds($auth->uid);
        global $auto;
        $auto = $rule_list; // 获取角色权限group表数据（这一步没问题）
        $tree = $menu->getMenuList($list, 0, $rule_list);
        json_return(0, '', $tree);
    }

    public function info()
    {
        $admin = [
            'id' => $this->__admin['id'],
            'name' => $this->__admin['name'],
            'mobile' => $this->__admin['mobile'],
            'nickname' => $this->__admin['nickname'],
            'avatar' => $this->__admin['avatar'],
            'email' => $this->__admin['email'],
            'remark' => $this->__admin['remark'],
            'status' => $this->__admin['status'],
        ];

        // test
        $admin['roles'] = ['admin'];

        json_return(0, '', $admin);
    }

    public function login()
    {
        if ($this->User->isLogin())
            json_return(0, '', ['token' => $this->User->token]);

        if ($this->request->isPost()) {

            $Login = new Login();

            $username = $this->request->param('username');
            $password = $this->request->param('password');

            $data = [
                'username'  => $username,
                'password'  => $password,
            ];

            //验证数据  BEGIN
            $rule = [
                ['username', 'require', 'Username or password can not be empty'],
                ['password', 'require', 'Username or password can not be empty'],
            ];

            // 是否开启验证码判断
            if ($this->Captcha) {
                $captcha = $this->request->param('captcha');
                $captcha_token = $this->request->param('captcha_token');
                $data['captcha'] = $captcha;
                $rule[] = ['captcha', 'require', 'Verification code can not be empty'];
            }

            $validate = new Validate($rule);
            $result = $validate->check($data);

            //验证数据  END
            if (!$result)
                json_return(1, $validate->getError());

            // 开启了验证码验证，使用Captcha API进行验证
            if ($this->Captcha) {
                $Captcha = new Captcha();
                if (!$Captcha->verify($captcha, $captcha_token))
                    json_return(1, 'Verification code is incorrect', ['msg' => $Captcha->getError()]);
            }

            // 验证登录合法性
            $result = $Login->login($username, $password);

            if ($result) {
                $this->log(lang('Login'));

                json_return(0, '', ['token' => $result['token']]);
            } else {
                $msg = $Login->getError();
                $msg = $msg ? $msg : 'Username or password is incorrect';

                json_return(1, $msg);
            }
        }
    }

    public function logout()
    {
        $admin = $this->User->admin;
        $admin->token = '';
        $admin->save();
        Session::delete('admin');
        Cookie::delete('Admin-Token');
        json_return(0, '注销成功');
    }
}
