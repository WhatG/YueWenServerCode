<?php
/**
 * Created by PhpStorm.
 * User: longer
 * Date: 2017/5/6
 * Time: 19:21
 */
require './utils.php';
$time = time();

$filecount = $_POST['filecount']?(int)$_POST['filecount']:false;
if(!$filecount||$filecount<1){
    die(toJsonString(-10,"缺少必须的字段"));
}
$base_path = './images/';
$target_path = '';
$image_url = '';

for($i = 0;$i<$filecount;$i++){
    $filename = 'file'.$i;
    $type = substr($_FILES[$filename]['type'],strpos($_FILES[$filename]['type'],"/")+1);
    $target_path = $base_path.$time."_".$i.".".$type;
    $move_result = move_uploaded_file($_FILES[$filename]['tmp_name'],$target_path);
    if($move_result){
        $image_url .=substr($target_path,1).",";

    }else{
        die(toJsonString(-6,'图片上传失败'));
    }
}
$image_url = rtrim($image_url,',');
$data = array();
$data[] = array('url'=>$image_url);
die(toJsonString(0,'图片上传成功',$data));

?>






