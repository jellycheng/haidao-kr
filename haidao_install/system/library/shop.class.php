<?php
class shop {
    public function __construct() {
        $this->api = 'market.haidao.la/api.php';
        $this->cloud = unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
        $this->data['timestamp'] = TIMESTAMP;
        $this->data['token'] = $this->cloud['token'];
        $this->data['site_id'] = $this->cloud['identifier'];
    }
    /**
     * [check_sign description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function check_sign($params){
        if(empty($params['sign']) || empty($params['site_id'])){
            $this->code = -20001;
            $this->error = '签名或站点标识不能为空';
            return false;
        }

        if(get_sign($params,$this->cloud['key']) != $params['sign']){
            $this->code = -20002;
            $this->error = '通信密钥或站点标识错误';
            return false;
        }
        return true;
    }
    /**
     * [_notify 操作回调]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function _notify($branch_id = 0,$type = '',$version = ''){
        $this->data['method'] = 'shop.application.status';
        $this->data['branch_id'] = $branch_id;
        $this->data['type'] = $type; 
        $this->data['version'] = $version;
        $info = Http::getRequest($this->api,$this->data);
        return TRUE;
    }
    /**
     * [get_plugin ]
     * @param  [type] $branch_id      [description]
     * @return [type]              [description]
     */
    public function get_plugin($branch_id = 0){
        $this->data['method'] = 'shop.application.update_download';
        $this->data['branch_id'] = $branch_id;
        return $this->data;
    }
    /**
     * [get_plugin_info 获取插件信息]
     * @return [type] [description]
     */
    public function get_branch_upgrade($branch_ids = array()){
        $this->data['method'] = 'shop.application.branch';
        $this->data['ishistory'] = 1;
        $this->data['branch_ids'] = implode(',',$branch_ids);
        $info = json_decode(Http::getRequest($this->api,$this->data),TRUE);
        return $info;
    }
    /**
     * [get_branch_auth 获取列表]
     * @return [type] [description]
     */
    public function get_branch_auth(){
        $this->data['method'] = 'shop.application.auths';
        $lists = json_decode(Http::getRequest($this->api,$this->data),TRUE);
        return $lists['result'];
    }
}
	