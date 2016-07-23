<?php
    if(!defined('IN_ADMIN')) {
        exit('Access Denied');
    }
    $plugins = cache('plugins');
    $plugins = $plugins[$_GET['mod']];
    $partook =  cache('qr_code','','plugin');
    if(IS_POST){
        $status = cache('qr_code',$_GET['status'],'plugin');
        if(!$status){
            showmessage('保存失败！');
        }else{
            showmessage('保存成功！');
        }
    }else{
        include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
    }

