<?php
class plugin_order_auto_cancel extends plugin{

	public function global_footer(){
        return '<script type="text/javascript" src="'.__ROOT__.'plugin.php?id=order_auto_cancel:set_order_cancel" ></script>';
	 }

	//执行详情页钩子
	public function order_detail_header_extra_info(){
		$params['sub_sn'] = $_GET['sub_sn'];
		$result = (int) $this->_little_payment_time($params);
		if($result){
			return '<div class="fl" style="height:40px;line-height:40px;padding:3px;margin-left:100px;">
				<span class="timer"  data-time="'.$result.'">温馨提示：订单将在<em class="m text-mix">00</em>分<em class="s text-mix">00</em>秒后自动取消，请尽快完成支付!</span>
			</div>
			<script type="text/javascript">$(".timer").timer();</script>';
		}
	}

	//执行下单成功钩子
	public function payment_header_extra_info(){
		$params['order_sn'] = $_GET['order_sn'];
		$result = (int) $this->_little_payment_time($params);
		if($result){
			return '<div class="fl" style="height:40px;line-height:40px;padding:3px;margin-left:100px;">
				<span class="timer"  data-time="'.$result.'">温馨提示：订单将在<em class="m text-mix">00</em>分<em class="s text-mix">00</em>秒后自动取消，请尽快完成支付!</span>
			</div>
			<script type="text/javascript">$(".timer").timer();</script>';
		}
	}

	/*返回剩余支付时间*/
	private function _little_payment_time($params = array()){
		$info = cache('order_auto_cancel');
		if($info['status'] != 1) return false;
		$sqlmap['pay_type'] = 1;//在线支付
        $sqlmap['status'] = 1;//正常订单
        $sqlmap['pay_status'] = 0;//未支付
        if(!empty($params['sub_sn'])) $sqlmap['sub_sn'] = $params['sub_sn'];
        if(!empty($params['order_sn'])) $sqlmap['order_sn'] = $params['order_sn'];
        $order_id = model('order/order_sub')->where($sqlmap)->getField('order_id');
		if(empty($order_id)) return false;
		$system_time = model('order/order')->where(array('id'=>$order_id))->getField('system_time');
		$pay_time = (int)$system_time+(int)$info['time']*60-time();//剩余支付时间(秒)
		if((int)$pay_time <= (int)$info['time']*60 && (int)$pay_time > 0){
			return $pay_time;
		}
	}

}