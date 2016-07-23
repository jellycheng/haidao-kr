<?php
$url_forward = $_GET['url_forward'] ? $_GET['url_forward'] : url('member/index/index');
$account_type = array('alipay' => '支付宝','qq' => 'qq','sina' => '新浪微博','wechat' => '微信');

$method = '_login';
require_cache(PLUGIN_PATH.PLUGIN_ID.'/library/login_factory.class.php');
$login_factory =  new login_factory($_GET['login_code']);
$user_info = $login_factory->$method();
$_GET['openid'] = $user_info['openid'];
$_GET['account_name'] = $user_info['username'];
$_GET['avatar'] = $user_info['avatar'];
$_GET['account_type'] = $account_type[$_GET['login_code']];
unset($_GET['code']);
if(!$_GET['openid']){
	$message = '绑定失败，请稍后重试';
	exit(include TPL_PATH.'tip.tpl');
}
$_GET['type'] = $_GET['login_code'];
$minfo = model('member/member','service')->init();
if($minfo['id'] > 0){
	$result = model('#login#member_oauth','service')->check_user($_GET['openid'] , $_GET['login_code']);
	if(!$result){
		$data = array();
		$data['member_id'] = $minfo['id'];
		$data['openid'] = $_GET['openid'];
		$data['type'] = $_GET['type'];
		$data['dateline'] = TIMESTAMP;
		$data['account_name'] = $user_info['username'] ? $user_info['username'] : '';
		$bind_result = model('#login#member_oauth')->add($data);
		if(!$bind_result){
			$message = '帐号绑定失败';
			exit(include TPL_PATH.'tip.tpl');
		}
		redirect(__ROOT__.'plugin.php?id=login:third_login_bind');
	}else{
		$message = '该三方帐号已被绑定';
		exit(include TPL_PATH.'tip.tpl');
	}
}else{
	$result = model('#login#member_oauth','service')->check_user($_GET['openid'] , $_GET['login_code']);
	if (!$result) {
		// 未绑定
		$SEO = seo(0 ,'第三方登录账号绑定');
		if(defined('MOBILE')){
			include PLUGIN_PATH.PLUGIN_ID.'/template/wap/return.html';
		}else{
			include PLUGIN_PATH.PLUGIN_ID.'/template/pc/return.html';
		}
	} else {
		$userInfo = model('member/member')->find($result['member_id']);
		if (!$userInfo){
			$message = '该用户不存在';
			exit(include TPL_PATH.'tip.tpl');
		}
		switch ($userInfo['status']) {
			case '-1':
					$message = '该账号已被删除！';
					exit(include TPL_PATH.'tip.tpl');
				break;
			case '0':
				$message = '该账号未认证！';
				exit(include TPL_PATH.'tip.tpl');
				break;
			case '2':
				$message = '该账号已被禁用！';
				exit(include TPL_PATH.'tip.tpl');
				break;
			default:
				//登录
				dologin($userInfo['id'],$userInfo['password']);
				login_inc($userInfo['id']);
				redirect($url_forward);
				break;
		}
	}
}