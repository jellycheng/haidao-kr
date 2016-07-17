<?php
$url_forward = $_GET['url_forward'] ? $_GET['url_forward'] : url('member/index/index');
$member = model('member/member','service')->init();
if(IS_POST){
	$data['member_id'] = $member['id'];
	$data['type'] = $_GET['login_code']; 
	$result = model('login#member_oauth')->where($data)->find();
	if($result){
		showmessage('同一第三方帐号类型只能绑定一个！','',0);
	}
	$logins = cache('login','','plugin');
	require_cache(PLUGIN_PATH.PLUGIN_ID.'/library/login_factory.class.php');
    $login_factory = new login_factory($logins[$_GET['login_code']]['login_code']);
    $login_url = $login_factory->get_code();
    if (empty($login_url)) showmessage('第三方登录地址链接有误！请联系管理员');
    showmessage('第三方登录地址获取成功！' , $login_url, 1);
}else{
	$logins = cache('login','','plugin');
	$bind = model('#login#member_oauth')->where(array('member_id' => $member['id']))->getfield('type,account_name',TRUE);
	foreach ($logins as $key => $login) {
		if($login['enabled'] != 1) unset($logins[$key]);continue;
		$logins[$key]['account_name'] = $bind[$key];
	}
	$SEO = seo(0 ,'第三方登录账号绑定');
	if(defined('MOBILE')){
		include PLUGIN_PATH.PLUGIN_ID.'/template/wap/third_login_bind.html';
	}else{
		include PLUGIN_PATH.PLUGIN_ID.'/template/pc/third_login_bind.html';
	}
}