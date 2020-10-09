<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\exception\HttpResponseException;

/**
 * 接口统一JSON返回,在任何位置直接断点返回  lang转换返回信息文本
 */
function json_return( $code, $msg='', $data=[]){
    $msg = $msg ? lang($msg) : '';
    $result = [ 'code'=>$code, 'msg'=> $msg, 'data'=>$data];
    throw new HttpResponseException(json($result));
}

/**
 * 针对批量删除进行处理  '1,2,3' 转换为数组批量删除
 * @param type $ids
 * @return boolean
 */
function ds_delete_param($ids){
    //转换为数组
    $ids_array = explode(',', $ids);
    //数组值转为整数型
    $ids_array = array_map("intval", $ids_array);
    if(empty($ids_array)||  in_array(0, $ids_array)){
        return FALSE;
    }else{
        return $ids_array;
    }
}


/**
 * 获取客户端浏览器
 */
function getUserAgentBrowser(){
    $user_agent = request()->server('HTTP_USER_AGENT');
    if(strpos($user_agent, 'Chrome')) $browser = 'Chrome';
    else if(strpos($user_agent, 'Firefox')) $browser = 'Firefox';
    else if(strpos($user_agent, 'Safari')) $browser = 'Safari';
    else if(strpos($user_agent, 'MSIE')) $browser = 'IE';
    else $browser = 'Other';
    return $browser;
}


/**
 * 返回当前的毫秒时间戳
 */
function msectime(){
    list($msec, $sec) = explode(' ', microtime() );
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}


/**
 * 提交数据 
 * @param  string $url 请求Url
 * @param  string $method 请求方式
 * @param  array/string $headers Headers信息 
 * @param  array/string $params 请求参数
 * @return 
 */
function curlRequest($url, $method='GET', $headers=[], $params=[]){
    if (is_array($params)) {
        $requestString = http_build_query($params);
    } else {
        $requestString = $params ? : '';
    }
    if (empty($headers)) {
        $headers = array('Content-type: application/json'); 
    } elseif (!is_array($headers)) {
        parse_str($headers,$headers);
    }

    // setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // setting the POST FIELD to curl
    switch ($method){  
        case "GET" : curl_setopt($ch, CURLOPT_HTTPGET, 1);break;  
        case "POST": curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;  
        case "PUT" : curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");   
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;  
        case "DELETE":  curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);break;  
    }
    // getting response from server
    $response = curl_exec($ch);
    
    //close the connection
    curl_close($ch);
    
    //return the response
    if (stristr($response, 'HTTP 404') || $response == '') {
        return array('Error' => '请求错误');
    }
    return $response;
} 


/**
 * 批处理提交数据 
 * @param  array $data 批处理数组 下标url为必须，其他可选参数 method、headers、data
 * @param  string $timeout 请求超时时间
 * @return 
 */
function multiCurlRequest($data = [], $timeout = 120)
{
    if(!$data) return [];
    $request = [];
    $requestResource = curl_multi_init();
    foreach ($data as $k => $v) {

        if (!isset($v['url']) || !$v['url']) continue;

        $url = $v['url'];

        $method = isset($v['method']) ? $v['method'] : 'GET';

        $headers = isset($v['headers']) ? $v['headers'] :  ['Content-type: application/json'];

        $requestString = isset($v['data']) ? is_array($v['data']) ? http_build_query($v['data']) : $v['data'] : '';

        $option = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers, 
            CURLOPT_TIMEOUT => $timeout,// 请求超时时间,防止死循环
            CURLOPT_RETURNTRANSFER => 1,// 获取的信息以文件流的形式返回，而不是直接输出。
            CURLOPT_VERBOSE => 1, // 报告每一件意外的事情
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        switch ($method){ // 请求方式
            case "GET" :    $option[CURLOPT_HTTPGET] = 1; 
                            break;  
            case "POST":    $option[CURLOPT_POST] = 1;
                            $option[CURLOPT_POSTFIELDS] = $requestString; 
                            break; 
            case "PUT" :    $option[CURLOPT_CUSTOMREQUEST] =  "PUT";
                            $option[CURLOPT_POSTFIELDS] = $requestString; 
                            break; 
            case "DELETE":  $option[CURLOPT_CUSTOMREQUEST] =  "DELETE";
                            $option[CURLOPT_POSTFIELDS] = $requestString; 
                            break;
        }

        // 启动一个curl会话
        $request[$k] = curl_init();
        
        // 设置请求选项
        curl_setopt_array($request[$k], $option);
        // 添加请求句柄
        curl_multi_add_handle($requestResource, $request[$k]);
    }

    $running = null;
    $result = [];
    do {// 执行批处理句柄
        // CURLOPT_RETURNTRANSFER如果为0,这里会直接输出获取到的内容.如果为1,后面可以用curl_multi_getcontent获取内容.
        curl_multi_exec($requestResource, $running);
        // 阻塞直到cURL批处理连接中有活动连接,不加这个会导致CPU负载超过90%.
        curl_multi_select($requestResource);
    } while ($running > 0);

    foreach ($request as $k => $v) {
        $result[$k] = curl_multi_getcontent($v);
        curl_multi_remove_handle($requestResource, $v);
    }
    curl_multi_close($requestResource);

    return $result;
}

