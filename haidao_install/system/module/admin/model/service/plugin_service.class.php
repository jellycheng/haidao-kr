<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class plugin_service extends service {
	public $data = array();
	public function __construct() {
		$this->api = 'market.haidao.la/api.php';
        $this->plugin_db = $this->load->table('admin/plugin');
        $this->pluginvar_db = $this->load->table('admin/pluginvar');
        $this->plugin_down_path = CACHE_PATH.'plugin/';
    }
    /**
     * [renew 续费]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function renew($params){
    	$shop = new shop();
    	$shop->check_sign();
    	$data = $params['data'];
    	$plugins = cache('plugin_lists');
    	if(empty($data) || !is_array($data)){
    		$this->code = -20006;
    		$this->error = '参数错误';
    		return FALSE;
    	}
    	foreach ($data AS $plugin) {
    		$plugins[$plugin['branch_id']]['start_time'] = $plugin['start_time'];
    		$plugins[$plugin['branch_id']]['end_time'] = $plugin['end_time'];
    	}
    	cache('plugin_lists',$plugins);
    	return true;
    }
     /**
     * [get 执行插件安装流程]
     * @param  string  $identifier [description]
     * @param  boolean $is_down    [description]
     * @return [type]              [description]
     */
    public function install($params){
		if(!is_dir(CACHE_PATH.'plugin')) mkdir(CACHE_PATH.'plugin');

    	$shop = new shop();
    	$shop->check_sign($params);
    	$data = $params['data'];
    	foreach ($data AS $plugin) {
	    	//验证是否是推送的信息
	    	$file = $this->plugin_down_path.random(8).'.zip';
	    	if(!$file){
				$this->code = -20003;
				$this->error = '请检查目录权限';
				return FALSE;
			}
			$identifier = $plugin['identifier'];
			if(!$identifier){
				$this->code = -20004;
				$this->error = '插件标识丢失，请联系云商团队！';
				return FALSE;
			}
			$install_data = $shop->get_plugin($plugin['branch_id']);
			$this->downpack($file,$install_data);
			$expfile = $this->expfile($file,$identifier,$plugin['type']);
			if(!$expfile){
				$this->code = -20003;
				$this->error = '请检查目录权限';
				return FALSE;
			}
			if($plugin['type'] == 'plugin'){
				$this->_install($identifier,$plugin['branch_id']);
			}elseif($plugin['type'] == 'module'){
				$this->load->service('admin/module')->install($identifier,$plugin['branch_id']);
			}
			$version = $this->plugin_db->where(array('branch_id' => $plugin['branch_id']))->getfield('version');
			$shop->_notify($plugin['branch_id'],'install',$version);
    	}
		return true;
    }
    /**
     * [upgrade ]
     * @param  string $identifier [description]
     * @return [type]             [description]
     */
    public function upgrade($identifier = ''){
    	if(!$identifier){
    		$this->error = '应用标识不能为空';
    		return FALSE;
    	}
    	$branch_id = $this->plugin_db->where(array('identifier' => $identifier))->getfield('branch_id');
    	if(!$branch_id){
    		$plugin_folder = PLUGIN_PATH.$identifier.'/upgrade.xml';
    		if(!file_exists($plugin_folder) || !is_file($plugin_folder)){
    			$this->error = '当前版本已是最新！';
    			return FALSE;
    		}
    		return $this->_upgrade($identifier);
    	}else{
			$shop = new shop();
	    	$plugins = $this->get_new_plugin($branch_id);
	    	if(!$plugins){
	    		$this->error = '当前版本已是最新！';
	    		return FALSE;
	    	}
	    	if($plugins['type'] != 'plugin'){
	    		$this->error = '参数错误';
	    		return FALSE;
	    	}
	    	//解压
	    	foreach ($plugins['_history'] AS $plugin) {
	    		$file = $this->plugin_down_path.random(8).'.zip';
	    		$install_data = $shop->get_plugin($branch_id);
	    		$this->downpack($file,$install_data);
		    	$expfile = $this->expfile($file,$identifier,$plugins['type']);
		    	if(!$expfile){
					$this->error = '解压出错，请检查根目录权限';
					return FALSE;
				}
				//升级
				$result = $this->_upgrade($identifier,$branch_id);
			}
			$version = $this->plugin_db->where(array('branch_id' => $branch_id))->getfield('version');
			$shop->_notify($branch_id,'upgrade',$version);
    	}
    	return TRUE;
    }
    /**
     * [uninstall 卸载]
     * @param  string $identifier [description]
     * @return [type]             [description]
     */
    public function uninstall($identifier = ''){
		$data = $this->plugin_db->where(array('identifier' => $identifier))->field('branch_id,version')->find();
		$result = $this->_uninstall($identifier);
		if(!$result){
		  	$this->error = '卸载失败';
		  	return FALSE;
		}
		if($data['branch_id'] > 0){
		  	$shop = new shop();
		  	$shop->_notify($data['branch_id'],'uninstall',$data['version']);
		}
    	return TRUE;
    }
    /**
     * [downpack 下载插件]
     * @param  [type] $identifier [description]
     * @return [type]             [description]
     */
    private function downpack($file = '',$data = array()){
    	$ch = curl_init();
		//Initialize a cURL session.
		$fp = fopen($file, 'wb');
		curl_setopt($ch, CURLOPT_URL, $this->api);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
		//curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 64000);
		curl_setopt($ch, CURLOPT_POST, FALSE); // post传输数据
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);// post传输数据
		$res = curl_exec($ch);
		if (curl_errno($ch)){
			die(curl_error($ch));
		}else{
			curl_close($ch);
		}
		fclose($fp);
		return true;
	}
	/**
	 * [expfile 解压插件]
	 * @param  [type] $identifier [description]
	 * @return [type]             [description]
	 */
	private function expfile($file = '',$identifier = '',$type = 'plugin'){
		if(!$identifier){
			$this->error = '应用标识不能为空';
			return false;
		}
		$plugin = PLUGIN_PATH.$identifier;
		if(is_dir($plugin)){
			deldir($plugin);
		}
		if(is_file($file) && file_exists($file)){
			$archive = new pclzip($file);
			//解压
			switch ($type) {
				case 'plugin':
					$result = $archive->extract(PCLZIP_OPT_PATH, PLUGIN_PATH, PCLZIP_OPT_REPLACE_NEWER);
					break;
				case 'module':
					$result = $archive->extract(PCLZIP_OPT_PATH, MODULES_PATH, PCLZIP_OPT_REPLACE_NEWER);
					break;
				case 'template':
					$result = $archive->extract(PCLZIP_OPT_PATH, TPL_PATH, PCLZIP_OPT_REPLACE_NEWER);
					break;
				default:
					break;
			}
			if ($result == 0) {
				$this->error = lang('admin/upload_file_no_exist');
				return false;
			} 
			@unlink($file);
			return true;
		}else{
			$this->error = '插件不存在';
			return false;
		}
	}
	/**
	 * [install 安装插件]
	 * @param  [type] $identifier [description]
	 * @return [type]             [description]
	 */
	public function _install($identifier = '',$branch_id = 0){

		$plugin_folder = PLUGIN_PATH.$identifier;
		$xmldata  = $this->get_xml_config($identifier);
		if(!$xmldata) {
			$this->error = '没有找到配置文件'.$xmldata;
			return false;
		}
		$plugin_data = array(
			'available' => 0,
			'adminid' => $xmldata['Data']['plugin']['adminid'],
			'name' => $xmldata['Data']['plugin']['name'],
			'identifier' => $xmldata['Data']['plugin']['identifier'],
			'description' => $xmldata['Data']['plugin']['description'],
			'datatables' => $xmldata['Data']['plugin']['datatables'],
			'directory' => $xmldata['Data']['plugin']['directory'],
			'copyright' => $xmldata['Data']['plugin']['copyright'],
			'modules' => serialize($xmldata['Data']['plugin']['modules']),
			'version' => $xmldata['Data']['plugin']['version'],
			'author' => $xmldata['Data']['plugin']['author'],
		);
		if($branch_id) $plugin_data['branch_id'] = $branch_id;
		/* 执行安装文件 */
		if($xmldata['Data']['installsql'] && file_exists($plugin_folder.'/'.$xmldata['Data']['installsql'])) {
			$sql = file_get_contents($plugin_folder.'/'.$xmldata['Data']['installsql']);
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
			@unlink($plugin_folder.'/'.$xmldata['Data']['installsql']);
		}
		if($xmldata['Data']['installfile'] && file_exists($plugin_folder.'/'.$xmldata['Data']['installfile'])) {
			include $plugin_folder.'/'.$xmldata['Data']['installfile'];
		}
		$pluginid = $this->plugin_db->update($plugin_data);
		if (!$pluginid) {
			$this->error = $this->plugin_db->getError();
			return false;
		} else {			
			/* 创建插件字段 */
			$vars = array();
			foreach ($xmldata['Data']['var'] as $v) {
				$v['pluginid'] = $pluginid;
				$vars[] = $v;
			}
			$this->pluginvar_db->addAll($vars);
			/* 创建后台菜单 */
			$nodes = array();
			foreach($xmldata['Data']['plugin']['modules'] as $module) {
				if(is_numeric($module['type'])) {
					$nodes[] = array(
						'parent_id' => $module['type'],
						'name' => $module['menu'],
						'sort' => $module['displayorder'],
						'url' => url('admin/plugin/module', array('mod' => $module['name'])),
						'pluginid' => $pluginid,
					);
				}
			}
			if($nodes) $result = $this->load->table('node')->addAll($nodes);
			if(!$result){
				$this->code = -20005;
				$this->error = '操作失败,请勿重复安装';
				return false;
			}
			@unlink($plugin_folder.'/'.$xmldata['Data']['installfile']);
			@unlink($plugin_folder.'/config.xml');
			/* 更新缓存 */
			$this->build_cache();
			return true;
		}
	}
	/**
	 * [_upgrade 升级]
	 * @param  string  $identifier [description]
	 * @param  integer $branch_id  [description]
	 * @return [type]              [description]
	 */
	private function _upgrade($identifier = '',$branch_id = 0){
		$plugin_folder = PLUGIN_PATH.$identifier;
		$xmldata  = $this->get_xml_config($identifier,'upgrade');
		if(!$xmldata) {
			$this->error = '没有找到配置文件';
			return false;
		}
		if($xmldata['Data']['plugin']['adminid']) $plugin_data['adminid'] = $xmldata['Data']['plugin']['adminid'];
		if($xmldata['Data']['plugin']['name']) $plugin_data['name'] = $xmldata['Data']['plugin']['name'];
		if($xmldata['Data']['plugin']['identifier']) $plugin_data['identifier'] = $xmldata['Data']['plugin']['identifier'];
		if($xmldata['Data']['plugin']['description']) $plugin_data['description'] = $xmldata['Data']['plugin']['description'];
		if($xmldata['Data']['plugin']['datatables']) $plugin_data['datatables'] = $xmldata['Data']['plugin']['datatables'];
		if($xmldata['Data']['plugin']['directory']) $plugin_data['directory'] = $xmldata['Data']['plugin']['directory'];
		if($xmldata['Data']['plugin']['copyright']) $plugin_data['copyright'] = $xmldata['Data']['plugin']['copyright'];
		if($xmldata['Data']['plugin']['modules']) $plugin_data['modules'] = $xmldata['Data']['plugin']['modules'];
		if($xmldata['Data']['plugin']['version']) $plugin_data['version'] = $xmldata['Data']['plugin']['version'];
		if($xmldata['Data']['plugin']['author']) $plugin_data['author'] = $xmldata['Data']['plugin']['author'];
		
		if($xmldata['Data']['upgradesql'] && file_exists($plugin_folder.'/'.$xmldata['Data']['upgradesql'])) {
			$sql = file_get_contents($plugin_folder.'/'.$xmldata['Data']['upgradesql']);
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
			@unlink($plugin_folder.'/'.$xmldata['Data']['upgradesql']);
		}
		if($xmldata['Data']['upgradefile'] && file_exists($plugin_folder.'/'.$xmldata['Data']['upgradefile'])) {
			include $plugin_folder.'/'.$xmldata['Data']['upgradefile'];
		}
		$result = $this->plugin_db->where(array('identifier' => $identifier))->save($plugin_data);
		$pluginid = $this->plugin_db->where(array('identifier' => $identifier))->getField('pluginid');
		if ($result === FALSE) {
			$this->error = $this->plugin_db->getError();
			return false;
		} else {			
			/* 创建插件字段 */
			$vars = array();
			foreach ($xmldata['Data']['var'] as $v) {
				$v['pluginid'] = $pluginid;
				$vars[] = $v;
			}
			$this->pluginvar_db->addAll($vars);
			/* 创建后台菜单 */
			$nodes = array();
			foreach($xmldata['Data']['plugin']['modules'] as $module) {
				if(is_numeric($module['type'])) {
					$nodes[] = array(
						'parent_id' => $module['type'],
						'name' => $module['menu'],
						'sort' => $module['displayorder'],
						'url' => url('admin/plugin/module', array('mod' => $module['name'])),
						'pluginid' => $pluginid,
					);
				}
			}
			if($nodes) $result = $this->load->table('node')->addAll($nodes);
			if(!$result){
				$this->error = '操作失败';
				return false;
			}
			@unlink($plugin_folder.'/'.$xmldata['Data']['upgradefile']);
			@unlink($plugin_folder.'/upgrade.xml');
			/* 更新缓存 */
			$this->build_cache();
			return true;
		}
	}
	/**
	 * [get_version 检查版本信息]
	 * @param  [type] $branch_id [description]
	 * @return [type]             [description]
	 */
	private function get_new_plugin($branch_id = 0){
		if(!$branch_id){
			$this->error = '插件不存在';
			return FALSE;
		}
		$shop = new shop();
		$plugin = $shop->get_branch_upgrade((array)$branch_id);
		if($plugin['code'] != 10000){
			$this->error = '通信出错';
			return FALSE;
		}
		$version_remote = $plugin['result'][0];
		if(!$version_remote['_history']) {
			$this->error = '该插件没有新版本，无需升级';
			return FALSE;
		}
		return $version_remote;
	}
	/**
	 * [uninstall 卸载插件]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	private function _uninstall($identifier = '') {
		$plugin_folder = PLUGIN_PATH.$identifier;
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;
		$pluginid = $this->plugin_db->where($sqlmap)->getField('pluginid');
		if(!$pluginid) {
			$this->error = '插件不存在';
		}
		$this->pluginvar_db->where(array('pluginid' => $pluginid))->delete();
		$this->plugin_db->where($sqlmap)->delete();
		/* 执行删除文件 */
		if(file_exists($plugin_folder.'/uninstall.sql')) {
			$sql = file_get_contents($plugin_folder.'/uninstall.sql');
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
		}
		include $plugin_folder.'/uninstall.php';
		/* 卸载菜单 */
		$this->load->table('node')->where(array('pluginid' => $pluginid))->delete();
		deldir($plugin_folder);
		$this->build_cache();
		return true;
	}

	/**
	 * [available 启用禁用插件]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	public function available($identifier = '') {
		if(empty($identifier)) {
			$this->error = '参数错误';
			return false;
		}
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;
		$_available = $this->plugin_db->where($sqlmap)->getField('available');
		if($_available == 1) {
			$available = 0;
			$msg = '禁用';
		}else {
			$available = 1;
			$msg = '启用';			
		}
		$result = $this->plugin_db->where($sqlmap)->setField('available', $available);
		if(!$result) {
			$this->error = '插件'.$msg.'失败';
			return false;
		}
		$this->build_cache();
		return true;
	}
	/**
	 * [build_cache 生成缓存]
	 * @return [type] [description]
	 */
	public function build_cache() {
		$shop = new shop();
		$lists = $shop->get_branch_auth();
		$branch = $end = array();
		if($lists){
			foreach ($lists['lists'] AS $list) {
				if((TIMESTAMP < $list['start_time'] || TIMESTAMP > $list['end_time']) && $list['end_time'] > 0){
					$end[] = (int)$list['branch_id'];
				}
			}
		}
		if($end){
			$this->plugin_db->where(array('branch_id' => array('IN',$end)))->setField('available',0);
		}
		$sqlmap = array();
		$sqlmap['available'] = 1;
		$tmp = $this->plugin_db->where($sqlmap)->select();
		
		if($tmp) {
			$hooks = array();
			foreach ($tmp as $t) {
				$t['modules'] = unserialize($t['modules']);
				foreach($t['modules'] as $mod) {
					if($mod['type'] == 'hook') $hooks[$t['identifier']] = $t['branch_id'];
				}
				$plugins[$t['identifier']] = $t;
				$pluginvars[$t['identifier']] = $this->get_pluginvar($t['pluginid']);
			}
		}
		cache('hooks', $hooks);
		cache('plugins', $plugins);
		cache('pluginvars', $pluginvars);
	}

	/**
	 * [get_pluginvar 获取指定插件设置]
	 * @param  [type] $pluginid [description]
	 * @return [type]           [description]
	 */
	private function get_pluginvar($pluginid) {
		$sqlmap = array();
		$sqlmap['pluginid'] = $pluginid;
		return $this->load->table('pluginvar')->where($sqlmap)->getfield('variable, value', TRUE);
	}
	/**
	 * [get_xml_config 获取指定插件配置文件]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	private function get_xml_config($identifier = '',$type = 'config') {
		if (empty($identifier)) {
			$this->error = '参数错误';
			return FALSE;
		}
		$plugin_folder = PLUGIN_PATH.$identifier;
		if(!is_dir($plugin_folder) || !file_exists($plugin_folder)) {
			$this->error = '插件目录不存在';
			return FALSE;
		}
		$plugin_xml = $plugin_folder.'/'.$type.'.xml';
		if(!file_exists($plugin_xml)) {
			$this->error = '插件配置文件丢失（'.$plugin_xml.'）';
			return FALSE;
		}
		/* 检测重复安装 */
		$importtxt = @implode('', file($plugin_xml));
		$xmldata = xml2array($importtxt);
		return $xmldata;
	}
	/**
	 * [ajax_upgrade 获取更新列表]
	 * @return [type] [description]
	 */
	public function ajax_upgrade($flag = FALSE){
		if(!cache('plugin_lists','','common') || (boolean)$flag == TRUE){
			$shop = new shop();
			$info = $shop->get_branch_upgrade($this->plugin_db->getfield('branch_id',TRUE));
			$plugins = array();
	    	foreach ($info['result'] AS $key => $result) {
	    		$versions = array();
		    	$plugins[$result['id']] = $result;
	    		if($result['_history']){
			    	foreach ($result['_history'] AS $_history) {
			    		$versions[] = $_history['version'];
			    	}
	    			$plugins[$result['id']]['new_version'] = max($versions);
	    		}
	    	}
	    	cache('plugin_lists',$plugins,'common',array('expire' => 86400));
		}
	    return cache('plugin_lists');
	}
}                                              