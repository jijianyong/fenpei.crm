<?php

namespace app\admin\behavior;

class AdminLog
{
    
    public function run(&$params)
    {   
        // 记录每次post访问的操作日志
        if (request()->isPost())
            \app\admin\model\system\Adminlog::record();
            
    }

}
