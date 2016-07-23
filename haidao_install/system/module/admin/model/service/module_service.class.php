<?php
define('MODULES_PATH', APP_PATH.config('DEFAULT_H_LAYER').'/');

class module_service extends service
{
	public function __construct() {
		$this->modules = cache('module', '', 'common');
		$this->table = $this->load->table('admin/module');
		$this->plugin_down_path = CACHE_PATH.'plugin/';
		$this->plugin_service = $this->load->service('admin/plugin');
	}
	
	/* 获取所有模块 */
	public function lists() {
		$result = array();
		$dirs = glob(MODULES_PATH.'*');
		$addons = $this->table->select();
		$modules = array();
		foreach ($addons AS $module) {
			$modules[$module['identifier']] = $module['isenabled'];
		}
		foreach($dirs AS $dir) {
			if(!is_dir($dir)) continue;
			$name = basename($dir);
			$module = $this->_forment($modules,$name);
			if(!$module) continue;
			$result[$name] = $module;
		}
		return $result;
	}


	private function _forment($modules,$module) {
		$result = array(
			'identifier' => $module,
			'isinstall' => 0,//安装状态
			'isenabled' => 0,//安装状态
			'allow_istall' => true,
			'allow_unistall' => true,
			'icon' => MODULES_PATH.$module.'/preview.jpg',
			
		);
		/* 是否安装 */
		$isenabled = $modules[$module];
		if($isenabled == 1) {
			$result['isinstall'] = 1;
			$result['isenabled'] = 1;
			$config = $this->table->fetch_by_identifier($module);
		}elseif ($isenabled == 0 && !is_null($isenabled)) {
			$result['isenabled'] = 0;
			$result['isinstall'] = 1;
			$config = $this->table->fetch_by_identifier($module);
		} else {
			$file = MODULES_PATH.$module.'/config.xml';
			$config = @file_get_contents($file);
			$config = xml2array($config);
			if(!$config) return false;
			$config['name'] = $config['Data']['module']['name'];
			$config['version'] = $config['Data']['module']['version'];
			$config['description'] = $config['Data']['module']['description'];
		}		
		$result = array_merge($result, $config);	
		if($result['installfile'] && !is_file(MODULES_PATH.$module.$result['installfile'])) {
			$result['allow_install'] = false;
		}
		if($result['uninstallfile'] && !is_file(MODULES_PATH.$module.$result['uninstallfile'])) {
			$result['allow_unistall'] = false;
		}
		if(!is_file($result['icon'])) {
			$result['icon'] = __ROOT__.'statics/images/default_no_upload.png';
		}
	return $result;
	}
	/**
	 * [install 安装]
	 * @param  [type] $identifier [description]
	 * @return [type]             [description]
	 */
	public function install($identifier = ''){
		$module_folder = MODULES_PATH.$identifier;
		$xmldata  = $this->get_xml_config($identifier);
		if(!$xmldata) {
			$this->error = '没有找到配置文件';
			return false;
		}
		$module_data = array(
			'isenabled' => 0,
			'name' => $xmldata['Data']['module']['name'],
			'identifier' => $xmldata['Data']['module']['identifier'],
			'description' => $xmldata['Data']['module']['description'],
			'setting' => serialize($xmldata['Data']['module']['modules']),
			'version' => $xmldata['Data']['module']['version'],
			'author' => $xmldata['Data']['module']['author'],
		);
		
		/* 执行安装文件 */
		if($xmldata['Data']['installsql'] && file_exists($module_folder.'/'.$xmldata['Data']['installsql'])) {
			$sql = file_get_contents($module_folder.'/'.$xmldata['Data']['installsql']);
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
			@unlink($module_folder.'/'.$xmldata['Data']['installsql']);
		}
		if($xmldata['Data']['installfile'] && file_exists($module_folder.'/'.$xmldata['Data']['installfile'])) {
			include $module_folder.'/'.$xmldata['Data']['installfile'];
		}
		$moduleid = $this->table->add($module_data);
		if (!$moduleid) {
			$this->error = $this->table->getError();
			return false;
		} else {			
			/* 创建后台菜单 */
			$nodes = array();
			foreach($xmldata['Data']['module']['modules'] as $module) {
				if(is_numeric($module['type'])) {
					$nodes[] = array(
						'parent_id' => $module['type'],
						'name' => $module['menu'],
						'sort' => $module['displayorder'],
						'm' => $module['module'],
						'c' => $module['control'],
						'a' => $module['action']
					);
				}
			}
			if($nodes) $result = $this->load->table('node')->addAll($nodes);
			@unlink($module_folder.'/'.$xmldata['Data']['installfile']);
			@unlink($module_folder.'/config.xml');
			/* 更新缓存 */
			$this->build_cache();
			return true;
		}
	}
	/**
	 * [upgrade 升级]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	public function upgrade($identifier = ''){
		if(!$identifier){
    		$this->error = '应用标识不能为空';
    		return FALSE;
    	}
    	$branch_id = $this->table->where(array('identifier' => $identifier))->getfield('branch_id');
    	if(!$branch_id){
    		$module_folder = MODULES_PATH.$identifier.'/upgrade.xml';
    		if(!file_exists($module_folder) || !is_file($module_folder)){
    			$this->error = '当前版本已是最新！';
    			return FALSE;
    		}
    		return $this->_upgrade($identifier);
    	}else{
			$shop = new shop();
	    	$modules = $this->get_new_module($branch_id);
	    	if(!$modules){
	    		$this->error = '当前版本已是最新！';
	    		return FALSE;
	    	}
	    	if($modules['type'] != 'module'){
	    		$this->error = '参数错误';
	    		return FALSE;
	    	}
	    	//解压
	    	foreach ($modules['_history'] AS $module) {
	    		$file = $this->plugin_down_path.random(8).'.zip';
	    		$install_data = $shop->get_plugin($branch_id);
		    	$this->plugin_service->downpack($file,$install_data);
		    	$expfile = $this->plugin_service->expfile($file,$identifier,$modules['type']);
		    	if(!$expfile){
					$this->error = '解压出错，请检查根目录权限';
					return FALSE;
				}
				//升级
				$result = $this->_upgrade($identifier,$branch_id);
			}
			$version = $this->plugin_db->where(array('branch_id' => $branch_id))->getfield('version');
			$shop->_notify($branch_id,'update',$version);
		}
	}
	/**
	 * [upgrade 升级]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	public function _upgrade($identifier = '',$branch_id = 0){
		$module_folder = MODULES_PATH.$identifier;
		$xmldata  = $this->get_xml_config($identifier,'upgrade');
		if(!$xmldata) {
			$this->error = '没有找到配置文件';
			return false;
		}
		if($xmldata['Data']['module']['name']) $module_data['name'] = $xmldata['Data']['module']['name'];
		if($xmldata['Data']['module']['identifier']) $module_data['identifier'] = $xmldata['Data']['module']['identifier'];
		if($xmldata['Data']['module']['description']) $module_data['description'] = $xmldata['Data']['module']['description'];
		if($xmldata['Data']['module']['setting']) $module_data['copyright'] = $xmldata['Data']['module']['copyright'];
		if($xmldata['Data']['module']['version']) $module_data['version'] = $xmldata['Data']['module']['version'];
		if($xmldata['Data']['module']['author']) $module_data['author'] = $xmldata['Data']['module']['author'];
		
		if($xmldata['Data']['upgradesql'] && file_exists($module_folder.'/'.$xmldata['Data']['upgradesql'])) {
			$sql = file_get_contents($module_folder.'/'.$xmldata['Data']['upgradesql']);
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
			@unlink($module_folder.'/'.$xmldata['Data']['upgradesql']);
		}
		if($xmldata['Data']['upgradefile'] && file_exists($module_folder.'/'.$xmldata['Data']['upgradefile'])) {
			include $module_folder.'/'.$xmldata['Data']['upgradefile'];
		}
		$result = $this->table->where(array('identifier' => $identifier))->save($module_data);
		if ($result === FALSE) {
			$this->error = $this->table->getError();
			return false;
		} else {			
			/* 创建后台菜单 */
			$nodes = array();
			foreach($xmldata['Data']['module']['modules'] as $module) {
				if(is_numeric($module['type'])) {
					$nodes[] = array(
						'parent_id' => $module['type'],
						'name' => $module['menu'],
						'sort' => $module['displayorder'],
						'm' => $module['module'],
						'c' => $module['control'],
						'a' => $module['action']
					);
				}
			}
			if($nodes) $result = $this->load->table('node')->addAll($nodes);
			@unlink($module_folder.'/'.$xmldata['Data']['upgradefile']);
			@unlink($module_folder.'/upgrade.xml');
			/* 更新缓存 */
			$this->build_cache();
			return true;
		}
	}
	/**
     * [uninstall 卸载]
     * @param  string $identifier [description]
     * @return [type]             [description]
     */
    public function uninstall($identifier = ''){
    	$result = $this->_uninstall($identifier);
    	if(!$result){
			$this->error = '卸载失败';
			return FALSE;
		}
		$branch_id = $this->table->where(array('identifier' => $identifier))->getField('branch_id');
		if($branch_id){
    		$version = $this->table->where(array('branch_id' => $branch_id))->getfield('version');
			$shop = new shop();
			$shop->_notify($branch_id,'uninstall',$version);
		}
    	return TRUE;
    }
	/**
	 * [uninstall 卸载插件]
	 * @param  string $identifier [description]
	 * @return [type]             [description]
	 */
	private function _uninstall($identifier = '') {
		$module_folder = MODULES_PATH.$identifier;
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;
		$this->table->where($sqlmap)->delete();
		/* 执行删除文件 */
		include $plugin_folder.'/uninstall.php';
		/* 卸载菜单 */
		foreach($xmldata['Data']['module']['modules'] as $module) {
			if(is_numeric($module['type'])) {
				$nodes[] = array(
					'parent_id' => $module['type'],
					'name' => $module['menu'],
					'm' => $module['module'],
					'c' => $module['control'],
					'a' => $module['action']
				);
			}
		}
		$this->load->table('node')->where($nodes)->delete();
		deldir($module_folder);
		$this->build_cache();
		return true;
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
		$module_folder = MODULES_PATH.$identifier;
		if(!is_dir($module_folder) || !file_exists($module_folder)) {
			$this->error = '模块目录不存在';
			return FALSE;
		}
		$module_xml = $module_folder.'/config.xml';
		if(!file_exists($module_xml)) {
			$this->error = '插件配置文件丢失（'.$module_xml.'）';
			return FALSE;
		}
		/* 检测重复安装 */
		$importtxt = @implode('', file($module_xml));
		$xmldata = xml2array($importtxt);
		return $xmldata;
	}
	/**
	 * [build_cache 生成插件缓存]
	 * @return [type] [description]
	 */
	public function build_cache() {
		$shop = new shop();
		$lists = $shop->get_branch_auth('module');
		$branch = $end = array();
		if($lists['lists']){
			foreach ($lists['lists'] AS $list) {
				if((TIMESTAMP > $list['start_time'] && TIMESTAMP < $list['end_time']) || $list['end_time'] == 0){
					$end[] = $list['branch_id'];
				}
			}
		}
		$end[] = 0;
		$this->table->where(array('branch_id' => array('NOT IN',$end)))->setField('isenabled',0);
		
		$sqlmap = array();
		$sqlmap['isenabled'] = 1;
		$modules = $this->table->where($sqlmap)->getField('identifier, name', true);
		return cache('module', $modules, 'common');
	}
	/**
	 * [ajax_upgrade 获取更新列表]
	 * @return [type] [description]
	 */
	public function ajax_upgrade($flag = FALSE){
		if(!cache('module_lists','','common') || $flag == TRUE){
			$shop = new shop();
			$info = $shop->get_branch_upgrade($this->table->getfield('branch_id',TRUE));
	    	foreach ($info['result'] AS $key => $result) {
	    		$versions = array();
		    	foreach ($result['_history'] AS $_history) {
		    		$versions[] = $_history['version'];
		    	}
		    	if($result['now_version'] < max($versions)){
					$info['result'][$key]['new_version'] = max($versions);
				}
	    	}

	    	cache('module_lists',$info['result'],'common');
		}
		return cache('module_lists');
	}
	/**
	 * [get_version 检查版本信息]
	 * @param  [type] $branch_id [description]
	 * @return [type]             [description]
	 */
	private function get_new_module($branch_id = 0){
		if(!$branch_id){
			$this->error = '插件不存在';
			return FALSE;
		}
		$shop = new shop();
		$module = $shop->get_branch_upgrade((array)$branch_id);
		if($module['code'] != 10000){
			$this->error = '通信出错';
			return FALSE;
		}
		$version_remote = $module['result'][0];
		if(!$version_remote['_history']) {
			$this->error = '该插件没有新版本，无需升级';
			return FALSE;
		}
		return $version_remote;
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
		$_isenabled = $this->table->where($sqlmap)->getField('isenabled');
		if($_isenabled == 1) {
			$isenabled = 0;
			$msg = '禁用';
		}else {
			$isenabled = 1;
			$msg = '启用';			
		}
		$result = $this->table->where($sqlmap)->setField('isenabled', $isenabled);
		if(!$result) {
			$this->error = '模块'.$msg.'失败';
			return false;
		}
		$this->build_cache();
		return true;
	}
}