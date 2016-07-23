<?php
$url_forward = url('member/index/index');
$setting = cache('setting');
if(IS_POST){
	require_cache(PLUGIN_PATH.PLUGIN_ID.'/function/function.php');
	$result = model('member/member','service')->register($_GET);
	if($result){
			// 绑定第三方账号
		$_result = model('#login#member_oauth','service')->check_bind($result , $_GET['type']);
		if ($_result) {
			showmessage('该第三方登录账号已绑定！','',0);
		}
		$data = array();
		$data['member_id']  = $result;
		$data['openid']   = $_GET['openid'];
		$data['type']     = $_GET['type'];
		$data['dateline'] = TIMESTAMP;
		$data['account_name'] = $_GET['username'] ? $_GET['username'] : '';
		$result_add = model('#login#member_oauth')->add($data);
		if (!$result_add) showmessage('第三方登录绑定失败，请从新绑定');
		dologin($result,$_GET['password']);
		showmessage('第三方登录绑定成功', $url_forward, 1);
	}else{
		showmessage('注册失败','',0);
	}
}else{
	$sms_reg = false;
    $sms_enabled = model('notify/notify')->where(array('code'=>'sms','enabled'=>1))->find();
    if($sms_enabled){
        $sqlmap['id'] = 'sms';
        $sqlmap['enabled'] = array('like','%register_validate%');
        $sms_reg = model('notify/notify_template')->where($sqlmap)->find();
    }
	if(defined('MOBILE')){
		include PLUGIN_PATH.PLUGIN_ID.'/template/wap/reg_bind.html';
	}else{
		include PLUGIN_PATH.PLUGIN_ID.'/template/pc/reg_bind.html';
	}
}