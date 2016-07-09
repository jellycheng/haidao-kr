<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class module_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->m_service = $this->load->service('admin/module');
	}

	public function index() {
		$modules = $this->m_service->lists();
		$type = isset($_GET['status']) ? $_GET['status'] : 1;
		foreach ($modules as $key => $value) {
			switch ($type) {
				case '1':
					if(!($value['identifier'] && $value['isenabled'] == 1 && $value['isinstall'] == 1)) unset($modules[$key]);
					break;
				case '0':
					if(!($value['identifier'] && $value['isenabled'] == 0 && $value['isinstall'] == 1)) unset($modules[$key]);
					break;
				case '-1':
					if(!($value['identifier'] && $value['isenabled'] == 0 && $value['isinstall'] == 0)) unset($modules[$key]);
					break;
				default:
					break;
			}
		}
		$limit = $_GET['limit'] ? $_GET['limit'] : 20;
		$start = ($_GET['page']-1) * $limit;
		$lists = array_slice($modules,$start,$limit);
		$count = count($modules);
		$pages = $this->admin_pages($count, $limit);
		$this->load->librarys('View')->assign('lists',$lists);
		$this->load->librarys('View')->assign('pages',$pages);
		$this->load->librarys('View')->display('module_index');
	}
	/**
	 * [ajax_upgrade 获取更新]
	 * @return [type] [description]
	 */
	public function ajax_upgrade(){
		$lists = $this->m_service->ajax_upgrade($_GET);
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
		$result = $this->m_service->available($_GET['identifier']);
		if(!$result){
			showmessage($this->m_service->error);
		}
		showmessage('操作成功');
	}

	/**
	 * [install 升级]
	 * @return [type] [description]
	 */
	public function install(){
		$result = $this->m_service->install($_GET['identifier']);
		if($result){
			showmessage('安装成功！');
		}else{
			showmessage($this->m_service->error);
		}
	}
	/**
	 * [upgrade 升级]
	 * @return [type] [description]
	 */
	public function upgrade(){
		$result = $this->m_service->upgrade($_GET['identifier']);
		if($result){
			showmessage('更新成功！');
		}else{
			showmessage($this->m_service->error);
		}
	}
}