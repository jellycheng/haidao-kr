<?php
if (!defined('IN_ADMIN')) {
    exit('Access Denied');
}
// 2016.06.27 整合UCenter
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];

//检查是否安装过，没有安装先进行安装。
$config = cache('ucenter_config','','plugin');
    //json_decode(model('setting')->where(array('key' => 'uc_config'))->getField('value'), true);

if (IS_POST) {
    //如果是第一次保存配置数据
    if (!$config) {
        $app_type = 'OTHER';
        $app_name = model('setting')->where(array('key' => 'site_name'))->getField('value');
        $app_url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__;
        $app_charset = 'utf-8';
        $app_dbcharset = 'utf8';
        $ucapi = trim($_GET['ucapi']);
        $ucip = $_GET['ucip'] ? trim($_GET['ucip']) : "";
        $ucfounderpw = trim($_POST['ucfounderpw']);
        $postdata = "m=app&a=add&ucfounder=&ucfounderpw=" . urlencode($ucfounderpw)
            . "&apptype=" . urlencode($app_type)
            . "&appname=" . urlencode($app_name)
            . "&appurl=" . urlencode($app_url)
            . "&appip=&appcharset=" . $app_charset
            . "&appdbcharset=" . $app_dbcharset;

        $url = $ucapi . "/index.php";
        $post_data = $postdata;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        curl_close($ch);
        list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset) = explode('|', $result);
        $uccharset || showmessage('ucenter路径或者密码错误。');

        $config = array(
            'uc_id' => $appid,
            'uc_key' => $appauthkey,
            'uc_url' => $ucapi,
            'uc_ip' => $ucapi,
            'uc_connect' => 'mysql',
            'uc_charset' => $uccharset,
            'db_host' => $ucdbhost,
            'db_user' => $ucdbuser,
            'db_name' => $ucdbname,
            'db_pass' => $ucdbpw,
            'db_pre' => $uctablepre,
            'db_charset' => $ucdbcharset,
        );
    } else {
        $config = $_GET;
    }
    //$data['key'] = 'uc_config';
    //$data['value'] = json_encode($config);
    //var_dump($data);exit;
    //model('setting')->save($data);
    cache('ucenter_config',$config,'plugin');
    showmessage('ucenter配置完成。');

} else {
    //var_dump($config);
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}