<?php
class plugin_qr_code extends plugin{
		
	/**
	 * 二维码 QR_code_display ()
	 */

	public function detail_tab_right(){
		$goods_id = $this->load->librarys('View')->fetch('goods');
		$partook =  cache('qr_code','','plugin');
		$url.='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].substr($PHP_SELF,0,strrpos($PHP_SELF,'/')+1);
		$url.= '?m=goods&c=index&a=detail&sku_id='.$goods_id['sku_id'];
		if($partook==1){
			$html .= '<style>';
			$html .= '.qrcode-box{position:relative;width: 120px;height: 100%;margin-top: 0;background: #f7f7f7;margin-right: -1px}';
			$html .= '.qrcode-top{height: 29px;line-height: 29px;display: block;position: relative;z-index: 998;}';
			$html .= '.icon-qrcode{background: url(system/plugin/qr_code/template/images/icon_qrcode.png) no-repeat;}';
			$html .= '.qrcode{position: absolute;z-index: 9999;top: 30px;right: 10px;}';
			$html .= '.qrcode-top .text{display: inline-block;height: 100%; float: left;padding-left: 15px;margin-right: 10px;}';
			$html .= '.qrcode-box:hover .qrcode-top .text{color: #1380cb}';
			$html .= '.qrcode-top .qranchor{display: inline-block;width: 29px;height: 100%; float: left;background-position: -31px 7px;}';
			$html .= '.qrcode-box:hover .qrcode-top .qranchor{background-position: 0 7px;}';
			$html .= '.qrcode-con{width: 100%;padding: 10px;}';
			$html .= '.qrcode-border{position: absolute;width:100%;height: 135px;top:0;left: 0;background: #fff}';
			$html .= '</style>';
			$html .= '<script type="text/javascript" src="'.__ROOT__.'statics/js/jquery.qrcode.min.js?v='.HD_VERSION.'"></script>';
			$html .= '';
			$html .= '<li class="fr qrcode-box"><span class="qrcode-top"><span class="text">手机购买</span><span class="qranchor icon-qrcode"></span></span>';
			$html .= '<div class="qrcode-con hidden"><div class="qrcode" data-model="qrcode"></div></div><div class="border qrcode-border hidden"></div></li>';
			$html .= '<script type="text/javascript">$("[data-model=\'qrcode\']").qrcode({render: "table",width: 100,height:100,text:"'.$url.'",});</script>'; 
			$html .= '<script type="text/javascript">$(".qrcode-box").hover(function(){$(".qrcode-con").removeClass(\'hidden\');$(".qrcode-border").removeClass(\'hidden\');},function(){$(".qrcode-con").addClass(\'hidden\');$(".qrcode-border").addClass(\'hidden\');})</script>';
		}
		return $html;	
	} 

}