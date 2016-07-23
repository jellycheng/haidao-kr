<?php
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$config = cache('geetest_config', '', 'plugin');
include('geetestlib.php');

$GtSdk = new GeetestLib($config['id'], $config['key']);
session_start();
$user_id = "test";
$status = $GtSdk->pre_process($user_id);
$_SESSION['gtserver'] = $status;
$_SESSION['user_id'] = $user_id;
echo $GtSdk->get_response_str();