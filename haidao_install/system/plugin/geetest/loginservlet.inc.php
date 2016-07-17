<?php

$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$config = cache('geetest_config', '', 'plugin');
//var_dump($config);
include('geetestlib.php');

session_start();
$GtSdk = new GeetestLib($config['id'], $config['key']);
$user_id = $_SESSION['user_id'];
if ($_SESSION['gtserver'] == 1) {
    $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
    if ($result) {
        echo 'Yes!';
    } else{
        echo 'No';
    }
}else{
    if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
        echo "yes";
    }else{
        echo "no";
    }
}
?>
