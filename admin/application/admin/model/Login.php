<?php

namespace app\admin\model;

use Random;
use think\Model;
use think\Db;
use app\admin\model\User;
use app\admin\model\system\Admin;

class Login extends Model
{

    protected $_error = '';

    protected $loginwrong = 3; // 允许登录错误次数

    protected $loginlock = 3600; // 超出登录次数锁定多少秒

    /**
     * 管理员登录验证
     * @param   string $username 用户名
     * @param   string $password 密码
     * @return  boolean
     */
    public function login($username, $password)
    {
        $admin = Admin::get(['name' => $username]);
        if (!$admin) {
            $this->setError('Username or password is incorrect');
            return false;
        }
        if ($admin->status != 0) {
            $this->setError('Admin is forbidden');
            return false;
        }

        // 错误N次之后,禁用登陆N小时, 注意！！改了多少时间，在返回的错误信息里面也要改下提示
        if ($admin->loginfailure >= $this->loginwrong && time() - $admin->updatetime < $this->loginlock) {
            $this->setError( lang('Please try again after %d hour', ['1']) );
            return false;
        }

        if ($admin->password != $admin->encryptPassword( $password, $admin->salt ) ) {
            $admin->loginfailure++;
            $admin->updatetime = time();
            $admin->save();
            $this->setError('Username or password is incorrect');
            return false;
        }
        
        $admin->loginfailure = 0;
        $admin->logintime = time();
        $admin->updatetime = time();
        $admin->token = Random::uuid();
        $admin->save();

        // 权限判断
        $auth = User::instance(); 
        $auth->init($admin->token);

        // 记录登陆日志
        $this->recordLoginLog($admin->id);
    
        return [ 'token'=>$admin->token];
    }

    /**
     * 记录登陆日志
     * @param   int $uid 管理员ID
     * @return  boolean
     */
    public function recordLoginLog($uid)
    {
        $data = [
            'uid' => $uid,
            'ip' => request()->ip(),
            'browser' => getUserAgentBrowser(),
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'date' => date('Y-m-d', time()),
            'time' => time(),
        ];
        Db::name('admin_login_log')->insert($data);
        return true;
    }

    // 设置错误信息
    public function setError($error)
    {
        $this->_error = $error;
        return true;
    }

    // 获取错误信息
    public function getError()
    {
        return $this->_error ? lang($this->_error) : '';
    }
   
}
