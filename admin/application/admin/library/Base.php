<?php
/**
 * 基础判断类
 * jijianyong
 * 2020/9/7
 * 
 */
namespace app\admin\library;

use think\Lang;
use think\Controller;
use think\Request;
use app\admin\model\User;
use app\admin\model\system\Adminlog;
use think\Hook;

class Base extends Controller
{
	// 无需登录的方法,同时也就不需要鉴权了
    protected $noNeedLogin = [];

    // 无需鉴权的方法,但需要登录
	protected $noNeedRight = [];
    
    // 用户类
    protected $User = null;

    // 管理员信息
    protected $__admin = null;

	public function _initialize()
    {	
        // token
        $token = $this->request->server('HTTP_TOKEN','');
    
        // 初始化User
        $this->User = User::instance(['token'=>$token]);
		
		// 当前访问路径
		$__module = $this->request->module();
        $__controller = strtolower($this->request->controller());
        $__action = strtolower($this->request->action());
        // 多级控制器会出现.的符号，替换成/
        $path = str_replace('.', '/', $__controller) . '/' . $__action;
    
        // 权限判断
        if( !$this->actionMatchArray($this->noNeedLogin) ){
            $demo = request('name');
            if( !$this->User->isLogin() ){ // 判断是否登陆
                json_return( 101, 'The login information is invalid' );
            }
            if ( !$this->actionMatchArray($this->noNeedRight) ) { // 判断是否需要验证权限
                if ( !$this->User->check( $path ) ) { // 判断权限
                    json_return( 102, 'You have no permission' );
                }
            }
        }

        // 绑定行为，在程序结束时调用行为方法录入到日志中
        // Hook::add('response_end', 'app\\admin\\behavior\\AdminLog');

        // 储存用户
        if( $this->User->isLogin() )
            $this->__admin = $this->User->admin->toArray();
        
        //加载当前控制器多语言文件
        $this->loadlang($__controller);

    }

    // 记录日志
    public function log( $remark='' )
    {   
        Adminlog::setTitle($remark);
    }

    // 加载语言文件
    protected function loadlang($name)
    {
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    // 检测当前控制器和方法是否匹配传递的数组
    public function actionMatchArray($arr = [])
    {
        $arr = array_map('strtolower', $arr);
        if (in_array(strtolower($this->request->action()), $arr) || in_array('*', $arr)) 
            return true;
        return false;
    }
}
