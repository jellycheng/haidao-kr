<?php
$url_forward = url('member/index/index');
if (IS_POST) {
	require_cache(PLUGIN_PATH.PLUGIN_ID.'/function/function.php');
	extract($_GET);
	if (empty($type))   showmessage('第三方登录类型有误！');
	if (empty($openid)) showmessage('第三方登录会员信息有误！');
	$sqlmap = array();
	$sqlmap['username'] = $username;
	$userInfo = model('member/member')->where($sqlmap)->find();
	if (!$userInfo || md5(md5($password).$userInfo['encrypt']) != $userInfo['password']) {
		showmessage('用户名或密码错误','',0);
	} else {
		switch ($userinfo['status']) {
			case '-1':
				showmessage('该账号已被删除！','',0);
				break;
			case '0':
				showmessage('该账号未认证！','',0);
				break;
			case '2':
				showmessage('该账号已被禁用！','',0);
				break;
			default:
				// 绑定第三方账号
				$result = model('#login#member_oauth','service')->check_bind($userInfo['id'] , $type);
				if ($result) {
					showmessage('该第三方登录账号已绑定！','',0);
				}
				$data = array();
				$data['member_id']  = $userInfo['id'];
				$data['openid']   = $openid;
				$data['type']     = $type;
				$data['dateline'] = TIMESTAMP;
				$data['account_name'] = $account_name;
				$_result = model('#login#member_oauth')->add($data);
				if (!$_result) showmessage('第三方登录绑定失败，请从新绑定','',0);
				dologin($userInfo['id'],$userInfo['password']);
				showmessage('第三方登录绑定成功', $url_forward, 1);
				break;
		}
	}
} else{
	if(defined('MOBILE')){
		include PLUGIN_PATH.PLUGIN_ID.'/template/wap/login_bind.html';
	}else{
		include PLUGIN_PATH.PLUGIN_ID.'/template/pc/login_bind.html';
	}
}