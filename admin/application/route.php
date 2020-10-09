<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
$DateBoo = true;
$other_module = \think\Config::get('other_module');
$pathinfo = strtolower(\think\Request::instance()->pathinfo());
$pathinfo = (in_array($pathinfo, $other_module))?$pathinfo.'/':$pathinfo;
foreach ($other_module as $key => $value) {
    if(preg_match('/^'.$value.'\//', $pathinfo)){
        $DateBoo = false;
        break;
    }
}
if($DateBoo) Route::bind(\think\Config::get('default_module'));

return [
    
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
