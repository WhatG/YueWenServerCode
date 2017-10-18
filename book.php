<?php
/**
 * Created by PhpStorm.
 * User: longer
 * Date: 2017/5/30
 * Time: 17:33
 */

include './utils.php';
$mysql_con = new mysqli('localhost:3306','root','1994woaini');
if(!$mysql_con){
    die(toJsonString(-3,"服务器出错",array()));
}
$mysql_con->query("set names 'utf8'");

if(!$mysql_con->query("use yuewen")){
    die(toJsonString(-8,"服务器出错",array()));
}
if(!isset($_POST['method'])){
    die(toJsonString(-10,"未知方法名"));
}
switch ($_POST['method'])
{
    case 'addbook':
        addbook();
        break;
    case 'getbooklist':
        getBookList();
        break;
    default:
        die(toJsonString(-10,"未知方法名"));
        break;

}

function addbook(){
    global $mysql_con;
    $time = time();

    $uid = isset($_POST['uid'])?$_POST['uid']:false;
    $bname = isset($_POST['bname'])?$_POST['bname']:false;
    $bauthor = isset($_POST['bauthor'])?$_POST['bauthor']:false;
    $brepublic = isset($_POST['brepublic'])?$_POST['brepublic']:false;
    $bdesc = isset($_POST['bdesc'])?$_POST['bdesc']:null;
    $bimage = isset($_POST['bimage'])?$_POST['bimage']:null;

    if(!$uid||!$bname||!$bauthor||!$brepublic){
        die(toJsonString(-10,"缺少必须的字段"));
    }

    $query_addbook = "insert into book (uid,bname,bauthor,brepublic,bdesc,images,btime) 
VALUE ('$uid','$bname','$bauthor','$brepublic','$bdesc','$bimage','$time')";

    $result = $mysql_con->query($query_addbook);
    if(!$result){
        die(toJsonString(-1,"上传失败"));
    }
    die(toJsonString(0,"上传成功"));
}

function getBookList(){
    $query_statment = "select u.uname,u.uicon,b.* 
                       from book b 
                       INNER JOIN userinfo u 
                       on b.uid = u.uid LIMIT 20";


    global $mysql_con;

    $uid = isset($_POST['uid'])?$_POST['uid']:false;

    if($uid){
        $query_statment = "select u.uname,u.uicon,b.* 
                       from book b 
                       INNER JOIN userinfo u 
                       on b.uid = u.uid and u.uid = '$uid' LIMIT 20";
    }


    $result = $mysql_con->query($query_statment);
    $data = array();
    if(!$result){
        die(toJsonString(-2,'查询失败'));
    }
    while(($data_fetch=$result->fetch_assoc())!=null){
        $data[] = $data_fetch;
    }
    echo toJsonString(0,'查询成功',$data);
    mysqli_free_result($data);
}

?>