<?php
if (!defined('IN_ADMIN')) {
    exit('Access Denied');
}
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
if (IS_POST) {
    // 自否需要登录
    // (登录)是否自动下单
    cache('price_notice_config', $_GET, 'plugin');
    showmessage('配置成功');
} else {

    $notice_list = model('price_notice')->select();
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}