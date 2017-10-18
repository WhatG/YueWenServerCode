<?php
include './utils.php';
$path = getLocalPath('http://192.168.1.117:8081/images/1494224989.jpg');
echo $path;
if(!$path){
    echo '外网资源';
}else{
    echo '内网资源';
}
if(is_file($path)){
    echo '是本地文件<br>';
}else{
    echo '不是本地文件<br>';
}
?>