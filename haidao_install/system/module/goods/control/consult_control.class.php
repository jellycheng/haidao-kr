<?php
class consult_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->consult_service = $this->load->service('goods/goods_consult');
	}
	/**
	 * [add 添加咨询]
	 */
	public function add(){
		$_GET['sku_id'] = (int) $_GET['sku_id'];
		if($_GET['sku_id'] < 1){
			showmessage(lang('_param_error_'));
		}
		$goods_detail = $this->load->table('goods_sku')->detail($_GET['sku_id'],true,'goods')->create_spec()->output();
		$cart_jump = $this->load->service('admin/setting')->get_setting('cart_jump');
		if($goods_detail === FALSE){
			showmessage(lang('goods/goods_goods_not_exist'));
		}
		if(checksubmit('dosubmit')){	
			if($this->member['id']){
				$_GET['mid'] = $this->member['id'];
				$_GET['username'] = $this->member['username'];
			}
			$result = $this->consult_service->add($_GET);
			if(!$result){
				showmessage($this->consult_service->error);
			}else{
				showmessage(lang('_operation_success_'),'',1,'','json');
			}
		}
		$SEO = seo('商品咨询');
		$this->load->librarys('View')->assign('cart_jump',$cart_jump);
		$this->load->librarys('View')->assign($goods_detail,$goods_detail);
		$this->load->librarys('View')->assign('SEO',$SEO);
		$this->load->librarys('View')->display('consult');
	}
	/**
	 * [see 查看咨询]
	 */
	public function see(){
		if(!$this->member['id']){
			showmessage(lang('_param_error_'));
			return FALSE;
		}
		$_GET['mid'] = (int) $this->member['id'];
		$result = $this->consult_service->see($_GET);
		if(!$result){
			showmessage($this->consult_service->error,'',1,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	
	/*
	 *	[统计咨询总数]
	 */
	public function ajax_cont(){
		$spu = (int)$_GET['spu'];
		$result = $this->consult_service->seecont($spu);
		echo $result;
	}
}