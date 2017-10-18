<?php
/**
 * Created by PhpStorm.
 * User: longer
 * Date: 2017/5/6
 * Time: 19:32
 */

$http = 'http://';
$test_br = "你好!";
function  toJsonString($error = 0,$msg=' ',$data = array())
{
    return json_encode(array('error'=>$error,'msg'=>$msg,'data'=>$data),JSON_UNESCAPED_UNICODE);
}

function getLocalPath($path){
    if(!(substr($path,0,7)=='http://')){
        return false;
    }

    $url_path = substr($path,7);
    $server_host = substr($url_path,0,strpos($url_path,':'));
    if(strcmp($server_host,$_SERVER['SERVER_ADDR'])!==0){
        return false;//外网资源。
    };
    return '.'.substr($url_path,strpos($url_path,'/'));
}
?>