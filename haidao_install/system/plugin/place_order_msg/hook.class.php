<?php
class plugin_place_order_msg extends plugin{
		
	public function create_order($member) {
		// echo '下单成功钩子';
		$this->execute($member);
	}


	/**
	 * [execute 通知执行接口]
	 */
	public function execute($member,$level = 100){
		$admin_msg = cache('place_order_msg');
		if(isset($admin_msg['phone'])){
	        $admin_msg['phone'] =preg_replace("/(\s)/",'',preg_replace("/(\n)|(\t)|(\')|(')|(，)/" ,',' ,$admin_msg['phone']));
	    }
		if($admin_msg['msg_status'] == 1 || !empty($admin_msg)){
			$member['member']['mobile'] = explode(',',$admin_msg['phone']);    //接受短信的管理员号码
		}else{
			continue;
		}
		//获取控制台下单成功短信模版
		$cloud = model('admin/cloud','service');
	    $data = $cloud->getsmstpl();
	    foreach ($data['result'] as $k => $v) {
	    	if($v['tpl_type'] == 'n_mobile_plug' && $v['id'] == $admin_msg['template_id']){
	    		$template = $v['content'];
	    	}
	    }

		//组织模版内容替换
		$setting = cache('setting', '', 'common');
		$replace = array(
			'{username}' => $member['member']['username'] ? $member['member']['username'] : '',
			'{site_name}' => $setting['site_name'],
			'{order_sn}' => $member['order_sn'],
			'{real_amount}' => $member['real_amount'],
		);

		//遍历	
		
		$mobile = $member['member']['mobile'];
		if(!$mobile) break;
		$template_replace = $this->template_replace();
		$format_data = array();
		foreach ($replace as $k => $v) {
			if(!empty($v)){
				$format_data[$template_replace[$k]] = $v;
			}
		}
		foreach ($mobile as $k => $v) {
			$data = array();
			$data['mobile'] = $v;
			$data['tpl_id'] = 224;
			// $data['tpl_id'] = $admin_msg['template_id'];
			$data['tpl_vars'] = $this->format_sms_data($format_data);

			if(!empty($data)){
				$params = unit::json_encode($data);
				model('notify/queue','service')->add('sms','send',$params,$level);
			} 
		}
		
		return TRUE;
	}

	/**
	 * [template_replace 替换模版内容]
	 * @return [type] [description]
	 */
	public function template_replace(){
		$replace = array(
			'{site_name}' => '{商城名称}',
			'{order_sn}' => '{主订单号}',
			'{order_amount}' => '{订单金额}',
			'{shop_price}' => '{商品金额}',
			'{real_amount}' => '{付款金额}'
		);
		return $replace;
	}

	public function format_sms_data($data){
		foreach ($data as $k => $v) {
			if(preg_match('/\{(.+?)\}/', $k)){
				$_data[preg_replace('/\{(.+?)\}/','$1',$k)] = $v;
			}
		}
		return $_data;
	}


}

