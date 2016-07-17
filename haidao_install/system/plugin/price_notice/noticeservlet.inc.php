<?php

if(!$_GET['price'] || !$_GET['email'] || !$_GET['mobile']) showmessage('请填写完整通知信息');
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$config = cache('price_notice_config', '', 'plugin');
if (!model('price_notice')->where(array('sku_id' => $_GET['sku_id'], 'mid' => $_GET['mid']))->find()) {
    $res = model('price_notice')->add($_GET);
    if ($res) showmessage('提醒添加成功');
    showmessage('提醒添加失败');
}
showmessage('已经设置过提醒了，降价了一定会通知您的。');
