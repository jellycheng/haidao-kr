<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class cloud_service extends service {   
   
    protected $_cloud = '';
    
    public function __construct() {
        $this->_cloud = new cloud();
        $this->config =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
    }
    /**
     * 获取绑定用户信息
     */
    public function get_account_info(){
        return $this->_cloud->get_account_info();
    }
    
    /**
     * 服务器通讯状态 
     */
    public function getcloudstatus(){
        return $this->_cloud->getcloudstatus();
    }  
    
    /**
     * 登录远程用户
     * @param type $account
     * @param type $password
     */
    public function getMemberLogin($account, $password) {
        //获取token
        $result = $this->get_access_token($account,$password);
        //获取用户信息
        $userinfo = $this->get_user_info($result['result']['access_token']);
        $cloud =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
        if(!$cloud){
            //绑定站点
            $site_info = $this->post_site_info($result['result']['access_token']);
        }else{
            $site_info = $this->get_site_info($result['result']['access_token'],$cloud['identifier']);
        }
         //构建信息
        if($site_info['code'] == 200 && !empty($site_info['result'])) {
            $_config = array(
                'username'   => $userinfo['result']['username'],
                'sms'        => $userinfo['result']['sms'],
                'coin'       => $userinfo['result']['coin'],
                'token'      => $result['result']['access_token'],
                'expires_in' => $result['result']['expires_in'],
                'identifier' => $site_info['result']['identifier'],
                'key'        => $site_info['result']['key'],
                'domain'     => $site_info['result']['domain'] ? $site_info['result']['domain'] : 'http://'.$_SERVER['HTTP_HOST'],
                'authorize'  => (int) $site_info['result']['authorize_status'],
                'authorize_endtime'     => $site_info['result']['authorize_endtime']
            );
            $config_text['__cloud__'] = authcode(serialize($_config),'ENCODE');
            $config = new Config();
            $r = $config->file('cloud')->note('云平台文件')->space(8)->to_require_one($config_text,null,1);
            if($r) {
                return true;
            }
        }
        return false;
    }
    /**
     * [get_access_token 获取token]
     * @return [type] [description]
     */
    private function get_access_token($account = '',$password = ''){
        if(!$account){
            $this->error = '帐号不能为空';
            return false;
        }
        if(!$password){
            $this->error = '密码不能为空';
            return false;
        }
        $data = array();
        $data['account'] = $account;
        $data['password'] = $password;
        $result = $this->_cloud->type('get')->data($data)->api('member/account.access_token');
        return $result;
    }
    /**
     * [get_user_info 获取用户信息]
     * @param  [type] $access_token [description]
     * @return [type]               [description]
     */
    private function get_user_info($access_token = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        $data = array();
        $data['access_token'] = $access_token;
        $info = $this->_cloud->type('get')->data($data)->api('member/get_user_info');
        return $info;
    }
    /**
     * [post_site_info 绑定站点]
     * @param  [type] $access_token [description]
     * @return [type]               [description]
     */
    private function post_site_info($access_token = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        $setting = cache('setting');
        $data = array();
        $data['access_token'] = $access_token;
        $data['domain'] = 'http://'.$_SERVER['HTTP_HOST'];
        $data['site_name'] = $setting['site_name'];
        $data['version'] = HD_VERSION;
        $data['server_ip'] = get_client_ip();
        $site_info = $this->_cloud->type('post')->data($data)->api('site/post_site_info');
        return $site_info;
    }
    /**
     * [get_site_info 获取站点信息]
     * @param  [type] $access_token [description]
     * @param  [type] $identifier   [description]
     * @return [type]               [description]
     */
    private function get_site_info($access_token = '',$identifier = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        if(!$identifier){
            $this->error = '站点标识不能为空';
            return false;
        }
        $data = array();
        $data['access_token'] = $access_token;
        $data['identifier'] = $identifier;
        $site_info = $this->_cloud->type('get')->data($data)->api('site/get_site_info');
        return $site_info;
    }

    /**
     * 实时更新站点用户信息
     * @param type $username    [平台用户名]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function update_site_userinfo($params) {
        $data['username'] = $params['username'];
        $data['version']    = HD_VERSION;
        $_result = $this->_cloud->data($data)->api('api.member.info');
        if($_result['code'] == 200 && !empty($_result['result'])) {
            $params['authorize']  = (int) $_result['result']['authorize_status'];
            $_config = array_merge($params,$_result['result']);
            $config_text['__cloud__'] = authcode(serialize($_config),'ENCODE');
            $config = new Config();
            $r = $config->file('cloud')->note('云平台文件')->space(8)->to_require_one($config_text);
            if($r) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取短信模版
     * @param type $tpl_type    [模版标识]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function getsmstpl() {
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['version'] = '2.0';
        return $this->_cloud->type('get')->data($data)->api('sms/template.groups');
    }
    /**
     * [getsmsnum 获取短信条数]
     * @return [type] [description]
     */
    public function getsmsnum(){
        $data = array();
        $data['access_token'] = $this->config['token'];
        return $this->_cloud->type('get')->data($data)->api('sms/balance');
    }
    /**
     * 发送短信
     * @param type $tpl_id      [模版ID]
     * @param type $mobile      [手机号码]
     * @param type $sms_sign    [短信签名]
     * @param type $tpl_vars    [模版变量(array)]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function send_sms($params) {
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['tpl_id']  = $params['tpl_id'];
        $data['sms_sign'] = $params['sms_sign'];
        $data['tpl_vars'] = $params['tpl_vars'];
        $data['mobile']  = $params['mobile'];
        return $this->_cloud->type('post')->data($data)->api('sms/send');
    }

    
    
    //获取最新版本
    public function api_product_version(){
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['version']      = HD_VERSION;
        $data['branch']       = HD_BRANCH;
        $data['allow_type']   = 'domain'; 
        return $this->_cloud->type('get')->data($data)->api('product/version');
    }

    //获取指定版本文件流
    public function api_product_downpack($branch=HD_BRANCH){
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['version']      = HD_VERSION;
        $data['branch']      = $branch;
        $data['allow_type']   = 'domain'; 
        return $this->_cloud->type('get')->data($data)->api('product/downpack');
    }
    
    //获取最新通知
    public function api_product_notify($version = HD_VERSION,$branch=HD_BRANCH){
        $data['version']      = $version;
        $data['branch']      = $branch;
        $result =  $this->_cloud->data($data)->api('api.member.notify');
        if($result['code'] == 200 && !empty($result['result'])) {
            cache('product_notify',$result['result']);
            return true;
        }
        cache('product_notify',null);
        return false;

    }
}