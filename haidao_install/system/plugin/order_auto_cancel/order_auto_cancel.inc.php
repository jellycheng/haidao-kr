<?php
if(!defined('IN_ADMIN')) {
    exit('Access Denied');
}
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$info = cache('order_auto_cancel');
if(IS_POST){
    $status = (int)$_POST['status'];
    $time = (int)$_POST['time'];
    if(!$time > 0) showmessage('请输入正确的时间');
    if(!in_array($status,array(0,1))) showmessage('状态不存在');
    $info = cache('order_auto_cancel', $_POST);
    if($info == ture){
        showmessage('设置成功');
    }else{
        showmessage('设置失败');
    }
}else{
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}