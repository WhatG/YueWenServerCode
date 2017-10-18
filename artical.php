<?php
/**
 * Created by PhpStorm.
 * User: longer
 * Date: 2017/5/12
 * Time: 15:09
 */
include './utils.php';
$mysql_con = new mysqli('localhost:3306','root','1994woaini');
if(!$mysql_con){
    die(toJsonString(-3,"服务器出错",array()));
}
if(!$mysql_con->query("use yuewen")){
    die(toJsonString(-8,"服务器出错",array()));
}
$mysql_con->query("set names 'utf8'");
switch ($_POST['method']){
    case 'newartical':
        uploadArtical();
        break;
    case 'getarticallist':
        getArticalList();
        break;
    case 'savedraft':
        saveDraft();
        break;
    case 'getdraftlist':
        getDraftList();
        break;
    default:
        //die(toJsonString(-9,"缺少必须的字段"));
        test();
}



function test(){
    global  $mysql_con;
    $data = array();
    $result = $mysql_con->query("select u.uname,u.uicon,a.* 
                                 from artical a 
                                 INNER JOIN userinfo u 
                                 on a.uid = u.uid");
    while(($data_fetch=$result->fetch_assoc())!=null){
        $data[] = $data_fetch;
    }
    die(toJsonString(0,'查询成功',$data));
}

function uploadArtical(){
    $did = isset($_POST['did'])?$_POST['did']:false;
    $uid = isset($_POST['userid'])?$_POST['userid']:false;
    if($uid===false){
        die(toJsonString(-10,'用户未登录'));
    }
    if(!isset($_POST['title'])||!isset($_POST['content'])){
        die(toJsonString(-10,'缺少必须的字段'));
    }
    $uid = (int)$uid;
    $title = $_POST['title'];
    $dscp = isset($_POST['desc'])?$_POST['desc']:null;
    $content = $_POST['content'];
    $time = time();
    $sql_insert =
        "insert into artical(uid,atitle,adscp,acontent,atime) 
         values 
         ('$uid','$title','$dscp','$content','$time')";

    global $mysql_con;
    $result = $mysql_con->query($sql_insert);

    if(!$result){
        die(toJsonString(-1,"上传失败",array()));
    }else{
        echo(toJsonString(0,'上传成功',array()));
        if(!$did){
            die();
        }
        $mysql_del_draft = "delete from draft WHERE aid = '$did'";
        $del_result = $mysql_con->query($mysql_del_draft);
        if(!$del_result){
            //产生了垃圾数据。
        }

    }
}
/*
 * params:uid,nouserinfo,
 */
function getArticalList(){
    $query_statment = "select u.uname,u.uicon,a.* 
                       from artical a 
                       INNER JOIN userinfo u 
                       on a.uid = u.uid";


    global $mysql_con;

    $uid = isset($_POST['uid'])?$_POST['uid']:false;

    if($uid){
        $query_statment = "select u.uname,u.uicon,a.* 
                       from artical a 
                       INNER JOIN userinfo u 
                       on a.uid = u.uid and u.uid = '$uid'";
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
}

function saveDraft(){
    global $mysql_con;

    $dtitle = isset($_POST['title'])?$_POST['title']:false;
    if(!$dtitle){
        die(toJsonString(-10,'缺少标题'));
    }
    $dcontent = isset($_POST['content'])?$_POST['content']:false;
    if(!$dcontent){
        die(toJsonString(-10,'缺少文章内容'));
    }
    $uid = isset($_POST['uid'])?$_POST['uid']:false;
    if(!$uid){
        die(toJsonString(-10,'缺少用户id'));
    }
    $ddesc = isset($_POST['desc'])?$_POST['desc']:null;

    $aid = isset($_POST['aid'])?$_POST['aid']:false;
    $dtime = time();
    $mysql_isexist = "select aid from draft WHERE aid  = '$aid'";
    $mysql_update = "update draft set atitle = '$dtitle',adscp = '$ddesc',atime = '$dtime',acontent = '$dcontent' 
                     WHERE aid = $aid";
    $mysql_insert = "insert into draft (uid, atitle, adscp, acontent,atime)
                     VALUE ('$uid','$dtitle','$ddesc','$dcontent','$dtime')";

    $mysql_result = false;
    if(!$aid){//第一次保存草稿，直接插入
        $mysql_result = $mysql_con->query($mysql_insert);
    }else{//已经存在草稿，更新数据。
        $mysql_result = $mysql_con->query($mysql_update);
        if(!$mysql_result){
            die(toJsonString(-2,'服务器错误'));
        }else{
            if(($mysql_result->num_rows)>0){
               $mysql_result = $mysql_con->query($mysql_update);
            }
        }

    }

    if(!$mysql_result){
        die(toJsonString(-1,'保存草稿失败'));
    }else{
        die(toJsonString(0,'保存成功'));
    }
}

function getDraftList(){
    global $mysql_con;
    $uid = isset($_POST['uid'])?$_POST['uid']:false;
    if(!$uid){
        die(toJsonString(-10,'缺少用户id'));
    }
    $query_statement = "select * from draft WHERE uid = $uid";
    $result = $mysql_con->query($query_statement);
    if(!$result){
        die(toJsonString(-2,'获取数据失败'));
    }
    $data = array();
    while(($data1=$result->fetch_assoc())!=null){
        $data[] = $data1;
    }
    die(toJsonString(0,'获取成功',$data));
}
?>