<?php
class plugin_login extends plugin{
	public function login_box_footer(){
		$logins = cache('login','','plugin');
		$html = '';
		foreach ($logins as $key => $login) {
			if($login['enabled'] == 1 && $login['login_code'] != 'wechat_wap'){
				$html .= '<a href="javascript:void(0)" class="third_login login-'.$login['login_code'].'" title="'.$login['login_name'].'" login-code="'.$login['login_code'].'">'.$login['login_name'].'</a>&nbsp;&nbsp;&nbsp;';
			}
		}
		$html .= '<script type="text/javascript">';
		$html .= '$(".third_login").bind("click",function(){';
		$html .=	'var login_code = $(this).attr("login-code");';
		$html .=	'$.post("plugin.php?id=login:third_login",{login_code : login_code},function(ret){';
		$html .=	'if (ret.status != 1) {';
		$html .=		'$.tips({';
		$html .=			'icon:"error",';
		$html .=			'content:ret.message';
		$html .=		'});';
		$html .=		'location.href = "'.url('member/member/index').'"';
		$html .=	'} else {';
		$html .=		'location.href = ret.referer;';
		$html .=	'}},"json")';
		$html .='})';
		$html .= '</script>';
		return $html;
	}
	public function menu_account_extra_menu(){
		$data = array();
		$data['m'] = 'plugin';
		$data['c'] = 'index';
		$data['id'] = 'login:third_login_bind';
		$data['name'] = '帐号绑定'; 
		return $data;
	}
	public function wap_login_footer(){
		$logins = cache('login','','plugin');
		$html  = ' <ul class="other-login">';
		foreach ($logins as $key => $login) {
			if($login['enabled'] == 1 && $login['login_code'] != 'wechat' && $login['login_code'] != 'wechat_wap'){
				$html .= '<li><a class="login-item login_'.$login['login_code'].'" login-code="'.$login['login_code'].'" href="javascript:;"></a><em class="mui-pull-right">|</em></li>';
			}
		}
		if(defined('IS_WECHAT')){
			$html .= '<li><a class="login-item login_'.$logins['wechat_wap']['login_code'].'" login-code="'.$logins['wechat_wap']['login_code'].'" href="javascript:;"></a><em class="mui-pull-right">|</em></li>';
		}
		$html .= '</ul>';
		$html .= '<script type="text/javascript">';
		$html .= 'mui(".other-login").on("tap",".login-item",function(){';
		$html .= 'var login_code = $(this).attr("login-code");';
		$html .= '$.post("plugin.php?id=login:third_login",{login_code:login_code},function(ret){';
		$html .= 'if (ret.status != 1) {';
		$html .= '$.tips({';
		$html .= 'content:ret.message,';
		$html .= 'callback:function() {';
		$html .= 'return false;';
		$html .= '}';
		$html .= '});';
		$html .= '} else {';
		$html .= 'window.location.href = ret.referer;';
		$html .= '}';
		$html .= '},"json")';
		$html .= '})';
		$html .= ' </script>';
		return $html;
	}
	public function wap_member_index_extra_info(){
		$data = array();
		$data['url'] = url('plugin/index/index',array('id' => 'login:third_login_bind'));
		$data['name'] = '帐号绑定';
		$data['desc'] = '修改绑定';
		$data['ico'] = __ROOT__.'system/plugin/login/statics/images/ico_bind';
		return $data;
	}
	public function no_login(){
		$logins = cache('login','','plugin');
		if(defined('IS_WECHAT') && $logins['wechat_wap']['config']['force'] == 1 && $logins['wechat_wap']['enabled'] == 1){
			redirect(url('plugin/index/index',array('id' => 'login:third_login','login_code' => 'wechat_wap')));
		}
	}
}