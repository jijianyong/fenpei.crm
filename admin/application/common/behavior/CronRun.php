<?php
/**
 * Created by PhpStorm.
 * User: yeqingyu
 * Date: 2018/12/17
 * Time: 20:34
 */

namespace app\common\behavior;

use think\Exception;
use think\Response;

class CronRun
{
    public function run(&$dispatch){
        $host_name = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
        $headers = [
            "Access-Control-Allow-Origin" => $host_name,
            "Access-Control-Allow-Credentials" => 'true',
            "Access-Control-Allow-Methods" => "POST, GET, OPTIONS, PUT, DELETE",
            "Access-Control-Allow-Headers" => "X-Token,x-uid,x-token-check,x-requested-with,content-type,Host,Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, token, field"
        ];
        if($dispatch instanceof Response) {
            $dispatch->header($headers);
        } else if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $dispatch['type'] = 'response';
            $response = new Response('', 200, $headers);
            $dispatch['response'] = $response;
        }
    }
}