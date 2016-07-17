<?php
if(!defined('IN_ADMIN')) {
    exit('Access Denied');
}
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$info = cache('place_order_msg');
// $admin_msg = cache('admin_msg');
if(IS_POST){
    $info = cache('place_order_msg', $_POST);
    if($info == ture){
        showmessage('设置成功');
    }else{
        showmessage('设置失败');
    }
    
}else{
    // $cloud = model('admin/cloud','service');
    // $data = $cloud->getsmstpl();
    // $sms_num = $cloud->getsmsnum();
    // foreach ($data['result'] as $k => $v) {
    //     $template[$v['tpl_type']][]= $v;
    // }
    // cache('admin_msg', $template['create_order']);
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}