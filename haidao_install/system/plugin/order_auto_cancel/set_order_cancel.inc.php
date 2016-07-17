<?php
$member = model('member/member','service')->init();
$info = cache('order_auto_cancel');
$time = (int) $info['time'];
if($info['status'] != 1) return false;
$sqlmap = array();
$sqlmap['buyer_id'] = $member['id'];
$sqlmap['pay_type'] = 1;//在线支付
$sqlmap['status'] = 1;//正常订单
$sqlmap['pay_status'] = 0;//未支付
//获取在线支付未付款订单
$orders = model('order/order')->where($sqlmap)->getField('id,system_time,sn',true);
if(!$orders) return false;
$order_ids = array();
foreach ($orders as $order_id => $order) {
    if(time()-(int)$order['system_time'] > $time*60){
        $order_ids[] = $order_id;
    }
}
if(empty($order_ids)) return false;
$sub_orders = model('order/order_sub')->where(array('order_id'=>array('IN',$order_ids)))->getField('sub_sn',true);
foreach ($sub_orders as $k => $sub_sn) {
    model('order/order_sub','service')->set_order($sub_sn,'order',2,array('msg'=>'订单超时自动取消','isrefund' => 0));
}