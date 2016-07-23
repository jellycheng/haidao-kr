<?php
if (!defined('IN_ADMIN')) {
    exit('Access Denied');
}
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$config = cache('geetest_config', '', 'plugin');
if (IS_POST) {
    // 配置
    // var_dump($_GET);
    cache('geetest_config', $_GET, 'plugin');
    showmessage('配置修改完毕。');
} else {
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}