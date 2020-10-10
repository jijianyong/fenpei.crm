<?php

/**
 * 资源自动分配类
 * jijianyong
 * 2020/9/23
 * updata jijianyong 2020/10/9
 */

namespace app\admin\controller;

class Automatic
{
    //  资源自动分配接口
    public function access()
    {

        //  从访问开始计算时间
        $static_time = microtime(true);

        // 获取还未分配的资源
        $order_data = db('sales_order')->where('remark', null)->select();

        // 获取还未分配足够的规则
        $distribution_data = db('auth_distribution')->where('remark<nums')->where('status', '0')->select();

        // 获取当前分配时间
        $time_h = date('H:i', time());

        // 如果分配规则全部已经完成，则跳出本次分配程序
        if (empty($distribution_data)) {
            return "资源分配任务已完成！";
        }

        // 遍历分配资源，将符合分配规则的数据打上用户标识（分步骤1、2、3、4）
        // 步骤1、循环资源
        foreach ($order_data as $ok => &$ord_value) {

            // 步骤2、循环分配规则
            foreach ($distribution_data as $dk => &$dis_value) {
                $d = $dis_value;
                $o = $ord_value;
                $remark = db('auth_distribution')->where('id', $dis_value['id'])->value('remark');
                $nums = $d['nums'];
                $startTime = $d['start_time'];
                $endTime = $d['end_time'];
                $oAddess = mb_convert_encoding(get_front_string($o['express_address'],"+"),'utf-8');
                if (empty($oAddess)) {
                    $oAddess = '空值';
                }
                $oExpluce = mb_convert_encoding(get_behind_string($o['express_address'],"+"),'utf-8');
                if (empty($oExpluce)) {
                    $oExpluce = '空值';
                }
                $dAddess = mb_convert_encoding($d['express_address'],'utf-8');
                $dExpluce = mb_convert_encoding($d['express_expluce'],'utf-8'); // 获取排除城市
                if (empty($dExpluce)) {
                    $dExpluce = '空值';
                }
                $oSource = mb_convert_encoding($o['express_source'],'utf-8');
                
                if (empty($oSource)) {
                    $oSource = '空值';
                }
                $dSource = mb_convert_encoding($d['express_source'],'utf-8');
                $oAdvisory = $o['express_advisory'];
                $dAdvisory = $d['express_advisory'];

                // 步骤3、判断数量、是否本轮赋过值、分配时间范围的条件，如果都复合则进行分配
                if ($remark < $nums && ($startTime < $time_h) && ($time_h < $endTime)) {

                    // 步骤4、判断此条数据符合哪条分配规则，如果有找到符合分配规则的就打上用户标识，并且此条分配规则第一遍已经分配
                    if (strpos($dAddess,$oAddess) !== false && strpos($dSource,$oSource) !== false && strpos($dExpluce,$oExpluce) === false && $oAdvisory == $dAdvisory) {
                        // 判断通过，数量增加1
                        $new_remark = $remark + 1;

                        // 更新distributon表
                        $tt = db('auth_distribution')->where("express_address='{$dAddess}' and express_source='{$dSource}' and express_advisory='{$dAdvisory}'")
                            ->update(['remark' => $new_remark]);

                        // 更新order表
                        $aa = db('sales_order')->where('id', $o['id'])->update(['remark' => $d['uid']]);
                    }
                }
            }
        }


        //获取程序执行结束的时间
        $end_time = microtime(true);

        //计算差值
        $total = $end_time - $static_time;

        // 分配完成后的提示
        echo "<br />当前页面执行时间为：{$total} 秒";
    }
}
