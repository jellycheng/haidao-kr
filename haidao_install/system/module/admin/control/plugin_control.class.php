<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class plugin_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->plugin_db = $this->load->table('admin/plugin');
		$this->plugin_service = $this->load->service('admin/plugin');
		$this->plugin_path = APP_PATH.'plugin/';
	}
	/**
	 * [index 插件列表]
	 * @return [type] [description]
	 */
	public function index() {
		$type = isset($_GET['status']) ? $_GET['status'] : 1;
		$plugin_folder = dir($this->plugin_path);
		$plugin_dir = str_replace(DOC_ROOT, '', $this->plugin_path);

		$addons = $this->plugin_db->select();
		foreach($addons as $plugin) {
			$plugin['modules'] = unserialize($plugin['modules']);
			$plugins[$plugin['identifier']] = $plugin;
		}

		while($entry = $plugin_folder->read()) {
			if(!in_array($entry, array('.', '..')) && is_dir($this->plugin_path.$entry)) {
				$entrydir = $this->plugin_path.$entry;
				$importfile = $entrydir.'/config.xml';
				if(!file_exists($importfile)) continue;
				$importtxt = @implode('', file($importfile));
				$xmldata = xml2array($importtxt);
				if (!in_array($entry, array_keys($plugins))) {
					$plugin = array(
						'pluginid'    => 0,
						'available'   => 0,
						'adminid'     => $xmldata['Data']['plugin']['adminid'],
						'name'        => $xmldata['Data']['plugin']['name'],
						'identifier'  => $xmldata['Data']['plugin']['identifier'],
						'description' => $xmldata['Data']['plugin']['description'],
						'datatables'  => $xmldata['Data']['plugin']['datatables'],
						'directory'   => $xmldata['Data']['plugin']['directory'],
						'author'      => $xmldata['Data']['plugin']['author'],
						'copyright'   => $xmldata['Data']['plugin']['copyright'],
						'directory'   => $xmldata['Data']['plugin']['directory'],
						'modules'     => $xmldata['Data']['plugin']['modules'],
						'version' => $xmldata['Data']['plugin']['version'],
					);
				} else {
					$plugin = $plugins[$entry];
				}
				$plugin['new_ver'] = $xmldata['Data']['plugin']['version'];				
				$plugins[$entry] = $plugin;
			}
		}
		foreach ($plugins as $key => $value) {
			switch ($type) {
				case '1':
					if(!($value['pluginid'] > 0 && $value['available'] == 1)) unset($plugins[$key]);
					break;
				case '0':
					if(!($value['pluginid'] > 0 && $value['available'] == 0)) unset($plugins[$key]);
					break;
				case '-1':
					if($value['pluginid'] > 0) unset($plugins[$key]);
					break;
				default:
					break;
			}
		}
		$limit = $_GET['limit'] ? $_GET['limit'] : 20;
		$start = ($_GET['page']-1) * $limit;
		$lists = array_slice($plugins,$start,$limit);
		$count = count($plugins);
		$pages = $this->admin_pages($count, $limit);
		$this->load->librarys('View')->assign('lists',$lists);
		$this->load->librarys('View')->assign('pages',$pages);
		$this->load->librarys('View')->display('plugin_index');
	}
	/**
	 * [module 插件管理]
	 * @return [type] [description]
	 */
	public function module(){
		$method = explode(':',$_GET['mod']);
		$mod = $method[1] ? $method[1] : $method[0];
		$plugin = $this->plugin_db->where(array('identifier' => $method[0]))->find();

		$plugin['modules'] = unserialize($plugin['modules']);
		$nav_title = '插件配置';
		foreach ($plugin['modules'] as $v) {
			if(is_numeric($v['type']) && $v['name'] == $mod) {
				$nav_title = $v['menu'];
				break;
			}
		}
		if(!$plugin) showmessage('插件不存在');
		if(!$mod) showmessage('参数错误');
		$plugin_folder = $this->plugin_path.$plugin['identifier'];
		define('PLUGIN_ID', $plugin['identifier']);
		$modfile = $plugin_folder.'/'.$mod.'.inc.php';
		if(!file_exists($modfile)) {
			$this->error = '模块文件不存在';
			return false;
		}
		include $modfile;
	}
	/**
	 * [install 插件安装]
	 * @return [type] [description]
	 */
	public function install(){
		$result = $this->plugin_service->_install($_GET['identifier']);
		if($result){
			showmessage('安装成功！');
		}else{
			showmessage($this->plugin_service->error);
		}
	}
	/**
	 * [upgrade 升级]
	 * @return [type] [description]
	 */
	public function upgrade(){
		$result = $this->plugin_service->upgrade($_GET['identifier']);
		if($result){
			showmessage('更新成功！');
		}else{
			showmessage($this->plugin_service->error);
		}
	}
	/**
	 * [uninstall 卸载插件]
	 * @return [type] [description]
	 */
	public function uninstall(){
		$result = $this->plugin_service->uninstall($_GET['identifier']);
		if($result){
			showmessage('卸载成功！');
		}else{
			showmessage($this->plugin_service->error);
		}
	}
	/**
	 * [ajax_status 获取更新状态]
	 * @return [type] [description]
	 */
	public function ajax_upgrade(){
		$lists = $this->plugin_service->ajax_upgrade($_GET);
		if($lists){
			showmessage('获取成功','',1,$lists);
		}else{
			showmessage('获取失败','',0);
		}
	}
	/**
	 * [available 更改插件状态]
	 * @return [type] [description]
	 */
	public function available() {
		$result = $this->plugin_service->available($_GET['identifier']);
		if(!$result){
			showmessage($this->plugin_service->error);
		}
		showmessage('操作成功');
	}
}