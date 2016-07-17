<?php
$logins = cache('login','','plugin');
if (empty($logins[$_GET['login_code']])) {
    showmessage('该登录方式不存在');
}
if ($logins[$_GET['login_code']]['enabled'] != 1) {
    showmessage('未开启该登录，请尝试其他登录方式');
}
$url_forward = $_GET['url_forward'] ? $_GET['url_forward'] : url('member/index/index');
$member = model('member/member','service')->init();
if($member['id'] > 0){
    showmessage('请勿重复登录', url('member/index/index'),0);
}
require_cache(PLUGIN_PATH.'login/library/login_factory.class.php');
if(IS_POST){
    $login_factory = new login_factory($logins[$_GET['login_code']]['login_code']);
    $login_url = $login_factory->get_code();
    if (empty($login_url)) showmessage('第三方登录地址链接有误！请联系管理员');
    showmessage('第三方登录地址获取成功！' , $login_url, 1);
}else{
    if($_GET['login_code'] == 'wechat_wap' && $logins[$_GET['login_code']]['config']['force'] == 1){
       $login_factory = new login_factory($logins[$_GET['login_code']]['login_code']);
        $login_url = $login_factory->get_code();
        redirect($login_url);
    }
}