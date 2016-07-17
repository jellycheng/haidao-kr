<?php
if(!defined('IN_ADMIN')) {
    exit('Access Denied');
}
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];
$info = cache('goods_buy_limit');
if(IS_POST){
    $_POST['start_time'] = strtotime($_POST['start_time']);
    $_POST['end_time'] = strtotime($_POST['end_time']);
    extract($_POST);
    if($start_time > $end_time) showmessage('开始时间不能大于结束时间');
    if(empty($sku_ids)) showmessage('限购商品不能为空');
    $info = cache('goods_buy_limit',$_POST);
    if($info == ture){
        showmessage('设置成功');
    }else{
        showmessage('设置失败');
    }
}else{
    $sku_ids = $info['sku_ids'];
    $skuids = array();
    foreach ($sku_ids as $sku_id => $num) {
        $skuids[] = $sku_id;
    }
    $skus = model('goods_sku')->where(array('sku_id'=>array('IN',$skuids)))->select();
    $lists = array();
    foreach ($skus as $key => $sku) {
        $item = array();
        $item['id'] = $sku['sku_id'];
        $item['pic'] = $sku['thumb'];
        $item['price'] = $sku['shop_price'];
        $item['number'] = $sku['number'];
        $item['nums'] = $sku_ids[$sku['sku_id']];
        $item['title'] = $sku['sku_name'];
        $lists[] = $item;
    }

    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}