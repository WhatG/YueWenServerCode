<?php

include './utils.php';

$sql_con = new mysqli('localhost:3306','root','1994woaini');
if(!$sql_con){
    die(json_encode(array('code' => -3,'msg' => '服务器无法连接数据库')));
}
$sql_con->select_db('yuewen');


$method = $_POST['method'];
switch ($method){
    case 'register':
        register();
        break;
    case 'login':
        login();
        break;
    case 'getusertoken':
        getIMToken();
        break;
    case 'usericon':
        userIcon();
        break;
    default:
        die(toJsonString(-4,'未知method'));
        break;
}

function login(){
    global $sql_con;
    $name = $_POST['name'];
    $password = $_POST['password'];

    $result = $sql_con->query("select uid,uname,uicon from userinfo where uname = '$name' and upassword = '$password'");
    if(!$result){
        die(toJsonString(-5,'用户名或密码错误'));
    }
    if($result->num_rows===0){
        die(toJsonString(-5,'用户名或密码错误'));
    }
    $id_result = $result->fetch_assoc();
    if($id_result!=null){
        die(toJsonString(0,'登录成功',array($id_result)));
    }
}

function register(){
    global $sql_con;

    $name = $_POST['name'];
    $password = $_POST['password'];
    $icon = isset($_POST['icon'])?$_POST['icon']:null;

    $nameexit = $sql_con->query("select uid from userinfo where uname = '$name'");
    if($nameexit->num_rows>0){
        die(toJsonString(-2,'昵称已被占用'));
    }

    $insertResult = $sql_con->query("insert into userinfo (uid,uname,upassword,uicon)
      values (0,'$name','$password','$icon')");

    if($insertResult){
        //query语句返回结果为true或false或者一个结果集
        die(toJsonString(0,'注册成功'));
    }else{
        die(toJsonString(-1,'插入数据库失败'));
    }
}
/*
 * 上传用户头像，插入用户数据失败要删除图片。
 * 更换头像要删除原来的图片。
 * 判断是否是初次上传。
 * 数据中存储的图片链接最终转换为本地相对路径。
 */
function userIcon(){
    global $sql_con;

    $id = $_POST['uid'];
    $url =$_POST['url'];//判断资源类型以及是否已经存储到images目录下。
//    if(!($local_path = getLocalPath($url))){
//        die(toJsonString(-7,'更换头像失败'));
    $result = $sql_con->query("update userinfo set uicon = '$url' where uid = '$id'");
    if(!$result){

        die(toJsonString(-4,'更换头像失败'));
    }else{
        $result1 = $sql_con->query("select uicon from userinfo WHERE uid = $id");
        $iconA = $result1->fetch_assoc();
        $data = array();
        $data[] = $iconA;

        die(toJsonString(0,'更换头像成功',$data));
    }
//    };
//    if(!$sql_con){
//        unlink($local_path);
//        die($array = toJsonString(-6,'更换头像失败'));
//    }
//
//    $sql_con->select_db("yuewen");
//    $select_result = $sql_con->query("select uicon from userinfo where uid = '$id'");
//    if(!$select_result){
//        unlink($local_path);
//        die(toJsonString(-2,'更换头像失败'));
//    }else{
//        $arr_result = $select_result->fetch_assoc();
//
//
//
//
//        if($arr_result['icon']!=null){//不是初次上传。
//            if(!unlink($arr_result['icon'])){//删除旧头像。
//                //服务器保存日志，有一个残余文件。
//            }
//        }
//    }
}
function getUserInfo(){

}
function getIMToken(){
    include './RongCloudAPI/rongcloud.php';
    $appKey = 'bmdehs6pbic6s';
    $appSecret = 'Po5tLJyjbtdyln';
    $jsonPath = "jsonsource/";
    $RongCloud = new RongCloud($appKey,$appSecret);
    $default_proatait = 'http://www.jf258.com/uploads/2014-08-17/175941870.jpg';
    global $sql_con;
    $uid = isset($_POST['uid'])?$_POST['uid']:false;
    if(!$uid){
        die(toJsonString(-10,'缺少用户名'));
    }
    else{
        $uid = (int)$uid;
    }

    $query_result = $sql_con->query("select * from userinfo  where uid = '$uid'");
    if(!$query_result||$query_result->num_rows<1){
        die(toJsonString(-2,'用户不存在'));
    }
    $userinfo = $query_result->fetch_assoc();
    // 获取 Token 方法
    $result = $RongCloud->user()->getToken($userinfo['uid'],$userinfo['uname'],$default_proatait);
    print_r($result);

}
?>
