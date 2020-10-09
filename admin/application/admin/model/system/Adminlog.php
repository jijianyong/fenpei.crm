<?php

namespace app\admin\model\system;

use think\Model;
use think\Db;
use app\admin\model\User;

class Adminlog extends Model
{
    //
    protected static $uid = '';
    // 标题
    protected static $title = '';
    // 提交内容
    protected static $content = '';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'tq_admin_log';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public static function setTitle($title)
    {
        self::$title = $title;
    }

    public static function setContent($content)
    {
        self::$content = $content;
    }

    public static function record($title = '')
    {   
        // 得到当前用户对象，获取UID
        $User = User::instance();
        $admin_id = $User->id ? : 0;

        // 得到所有提交的数据，过滤密码字段
        $content = self::$content;
        if (!$content) {
            $content = request()->param();
            foreach ($content as $k => $v) {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'pass') !== false || stripos($k, 'password') !== false)
                    unset($content[$k]);
            }
        }

        // 设置标题
        $title = self::$title;
        if (!$title)
            $title = str_replace('.', '/', request()->path() );
        
        $url = request()->url();
        if(strlen($url) > 500){
            $url = '太长不记录';
        }

        Db::name('admin_log')->insert([
            'title'     => $title,
            'content'   => !is_scalar($content) ? json_encode($content) : $content,
            'url'       => $url,
            'admin_id'  => $admin_id,
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'ip'        => request()->ip(),
            'createtime'=> time(),
        ]);
    }

}

