<?php
    if(!defined('IN_ADMIN')) {
        exit('Access Denied');
    }
    $plugins = cache('plugins');
    $plugins = $plugins[$_GET['mod']];
    $partook =  cache('partook_status','','plugin');
    if(IS_POST){
        if(empty($_GET['qzone'])) $_GET['qzone']='0';
        if(empty($_GET['tsina'])) $_GET['tsina']='0';
        if(empty($_GET['tqq'])) $_GET['tqq']='0';
        if(empty($_GET['weixin'])) $_GET['weixin']='0';
        if(empty($_GET['douban'])) $_GET['douban']='0';
        if(empty($_GET['renren'])) $_GET['renren']='0';
        if(empty($_GET['linkedin'])) $_GET['linkedin']='0';
        $status = cache('partook_status',$_GET,'plugin');
        if(!$status){
            showmessage('保存失败！');
        }else{
            showmessage('保存成功！');
        }
    }else{
        include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
    }

