<?php
class wap_hook extends Hook
{
	public function pre_system() {
		$mobile = new mobile;
		if(config('wap_enabled' ,'wap') && $mobile->isMobile() === TRUE) {
            define('MOBILE', TRUE);
            if(config('is_jump','wap') && config('wap_domain','wap') && stripos(strtolower($_SERVER['HTTP_HOST']), config('wap_domain','wap')) === FALSE && !defined('IN_ADMIN')) {
				$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$url = str_replace($_SERVER['HTTP_HOST'],config('wap_domain','wap'),$url);
                redirect((is_ssl() ? 'https://' : 'http://').$url);
            }
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') != false ){
                define('IS_WECHAT',TRUE);
            }
            config('TPL_THEME', null, 'wap');
        }
	}
	public function tmpl_compile($template){
		preg_match_all('/diy\s+(.+)}/',$template,$arr);
		if(empty($arr[0])){
			return $template;
		} 
		$compile_tmpl = '';
		$content_tmpl = '';
		foreach ($arr[0] AS $base_tpl) {
			$tpl = explode(' ',$base_tpl);
			$tmpl = $tpl[1] == 'content' ? base64_decode($tpl[2]) : json_decode(base64_decode($tpl[2]),TRUE);
			switch ($tpl[1]) {
				case 'global':
				$compile_tmpl .= '<style>body{background-color: '.$tmpl['bgcolor'].';}</style><header class="mui-bar mui-bar-nav header" style="background-color: '.$tmpl['headbg'].'">
<div class="logo mui-pull-left"><a href="{url(\'goods/index/index\')}"><img src="'.$tmpl['logo'].'" height="30" /></a></div><h1 class="mui-title">'.$tmpl['title'].'</h1><a href="{url(\'goods/index/category_lists\')}" class="hd-menu"><span class="mui-icon mui-icon-more"></span></a></header>';
					break;
				case 'ads':
					if($tmpl['show'] == 0){
						$content_tmpl .= '<div class="mui-slider"><div class="mui-slider-group">';
						foreach ($tmpl['imgs'] as $key => $value) {
							$content_tmpl .= '<a class="mui-slider-item" href="';
							if($value['url']){
								$content_tmpl .= $value['url'] .= '">';
							}else{
								$content_tmpl .= 'javascript:;">';
							}
							if($value['src']){
								$content_tmpl .= '<img src="'.$value['src'].'" />';
							}
							if($value['title']){
								$content_tmpl .= '<p class="mui-slider-title">'.$value['title'].'</p>';
							}
							$content_tmpl .= '</a>';
						}
						$content_tmpl .= '</div>';
						$content_tmpl .= '<div class="mui-slider-indicator">';
						foreach ($tmpl['imgs'] as $i => $val) {
							if($i == 0){
								$content_tmpl .= '<div class="mui-indicator mui-active"></div>';
							}else{
								$content_tmpl .= '<div class="mui-indicator"></div>';
							}
						}
						$content_tmpl .= '</div></div>';
					}else{
						$content_tmpl .= '<ul class="custom-image mui-clearfix">';
						foreach ($tmpl['imgs'] as $key => $value) {
							$content_tmpl .= '<li';
							if($tmpl['size']==1){
								$content_tmpl .= ' class="custom-image-small"';
							}
							$content_tmpl .= '><a href="';
							if($value['url']){
								$content_tmpl .= $value['url'].'">';
							}else{
								$content_tmpl .= 'javascript:;'.'">';
							}
							if($value['src']){
								$content_tmpl .= '<img src="'.$value['src'].'" />';
							}
							if($value['title']){
								$content_tmpl .= '<p class="hd-slider-title">'.$value['title'].'</p>';
							}
							$content_tmpl .= '</a></li>';
						}
						$content_tmpl .= '</ul>';
					}
					break;
				case 'goods':
					$content_tmpl .= '<ul class="custom-goods-items';
					switch ($tmpl['size']) {
						case 0:
							$content_tmpl .= ' custom-goods-single';
							break;
						case 2:
							$content_tmpl .= ' custom-goods-blend';
							break;
						case 3:
							$content_tmpl .= ' custom-goods-row';
							break;
						default:
							break;
					}
					$sku_url = '{url("goods/index/detail",array("sku_id" => $r[sku_id]))}';
					$content_tmpl .= ' mui-clearfix">';
					$content_tmpl .= '{hd:goods tagfile="goods" method="lists" catid="'.$tmpl['category'].'" num="'.$tmpl['goods_number'].'"}';
					$content_tmpl .= '{loop $data $r}';
					$content_tmpl .= '<li class="goods-item-list"><div class="list-item"><div class="list-item-pic"><a href="'.$sku_url.'" class="square-item"><img class="lazy" src="{SKIN_PATH}statics/images/loading.gif" data-original="{thumb($r[thumb],500,500)}"></a></div><div class="list-item-bottom"><div class="list-item-title"><a href="'.$sku_url.'">{$r[sku_name]}</a></div><div class="list-item-text"><span class="price-org">￥{$r[prom_price]}</span></div></div></div></li>';
					$content_tmpl .= '{/loop}';
					$content_tmpl .= '{/hd}';
					$content_tmpl .= '</ul>';
					break;
				case 'search':
				$content_tmpl .= '<div class="hd-search"><form name="form_search" action="{__ROOT__}" method="get"><input type="hidden" name="m" value="goods"><input type="hidden" name="c" value="search"><input type="hidden" name="a" value="search"><input type="search" placeholder="搜索商品名称" name="keyword"></form></div>';
					break;
				case 'spacing':
					$content_tmpl .= '<div class="custom-white" style="height: '.$tmpl['height'].'px;"></div>';
					break;
				case 'cube':
					$table = '';
					foreach (json_decode($tmpl['layout'],TRUE) AS $layouts) {
						$table .= '<tr>';
						foreach ($layouts AS $layout) {
							if($layout['flog']){
								$table .= '<td class="empty"></td>';
							}else{
								if($layout['width'] && $layout['height']){
									$imgs = '';
									$img = $tmpl['imgs'][$layout['index']];
									if($img['src']){
										$imgs = $img['src'] ? '<img src="'.$img['src'].'" />' : '';
										if($img['href']){
											$imgs = '<a href="'.$img['href'].'">'.$imgs.'</a>';
										}
									}
									$table .= '<td class="no-empty cols-'.$layout['width'].' rows-'.$layout['height'].'" colspan="'.$layout['width'].'" rowspan="'.$layout['height'].'">'.$imgs.'</td>';
								}
							}
						}
						$table .= '</tr>';
					}
					$content_tmpl .= '<table class="cube-table"><tbody>'.$table.'</tbody></table>';
					break;
				case 'content':
					$content_tmpl .= $tmpl;
					break;
				case 'coupon':

					$content_tmpl .= '<style>';
					$content_tmpl .= '.custom-coupon { padding: 10px; text-align: center; font-size: 0; }';
					$content_tmpl .= '.custom-coupon li { display: inline-block; margin-left: 2%; width: 32%; height: 67px; border: 1px solid #ff93b2; border-radius: 4px; background: #ffeaec; }';
					$content_tmpl .= '.custom-coupon li a { color: #fa5262 }';
					$content_tmpl .= '.custom-coupon li:nth-child(1) { margin-left: 0; }';
					$content_tmpl .= '.custom-coupon li:nth-child(2) { background: #f3ffef; border-color: #98e27f; }';
					$content_tmpl .= '.custom-coupon li:nth-child(2) a { color: #7acf8d; }';
					$content_tmpl .= '.custom-coupon li:nth-child(3) { background:#ffeae3; border-color:#ffa492; }';
					$content_tmpl .= '.custom-coupon li:nth-child(3) a { color: #ff9664; }';
					$content_tmpl .= '.custom-coupon .custom-coupon-price { height: 36px; line-height: 24px; padding-top: 12px; font-size: 24px; overflow: hidden; }';
					$content_tmpl .= '.custom-coupon .custom-coupon-price span { font-size: 16px; }';
					$content_tmpl .= '.custom-coupon .custom-coupon-desc { height: 24px; line-height: 20px; font-size: 12px; padding-top: 4px; overflow: hidden; }';
					$content_tmpl .= '</style>';
					$content_tmpl .= '<ul class="custom-coupon clearfix">';
					foreach (explode(',',$tmpl['coupon']) as $k => $v) {
						$member_info = model('member/member','service')->init();
						$coupon = model('coupon/coupon')->where(array('id'=>$v))->find();
						$data = array();
						$data['mid'] = $member_info['id'];
						$data['status'] = 1;
						$data['cid'] = $v;
						$member_num = model('coupon/coupon_list')->where($data)->count();
						if($coupon && $coupon['receive_num'] > $member_num){
							$rules = json_decode($coupon['rules'],true);
							$condition = sprintf("%.2f", $rules['condition']);
							$discount = sprintf("%.2f", $rules['discount']);
							if($coupon['type_time'] == 1){
								$content_tmpl .= '<li><a href="javascript:;" data-id="'.$v.'" class="receive_coupon" data-time="'.date('Y.m.d',$coupon['start_time']).'-'.date('Y.m.d',$coupon['end_time']).'">';
							}else{
								$content_tmpl .= '<li><a href="javascript:;" data-id="'.$v.'" class="receive_coupon" data-time="'.date('Y.m.d',time()).'-'.date('Y.m.d',time()+($coupon['time']*60*60*24)).'">';
							}
							$content_tmpl .= '<div class="custom-coupon-price"><span>￥</span>'.$discount.'</div>';
							if($condition == -1){
								$content_tmpl .= '<div class="custom-coupon-desc">不限金额</div>';
							}else{
								$content_tmpl .= '<div class="custom-coupon-desc">满'.$condition.'元可用</div>';
							}
							$content_tmpl .= '</a></li>';
						}	
					}
					$content_tmpl .= '</ul>';
					$content_tmpl .= '<script type="text/javascript" src="{SKIN_PATH}statics/js/haidao.js?v={HD_VERSION}"></script>';
					$content_tmpl .= '<script>';
					$content_tmpl .= '$(".receive_coupon").on("tap",function(){var id = $(this).data("id");var time = $(this).data("time");';
					$content_tmpl .= 'start_time = time.split("-")[0].replace(/\./g,"-");end_time = time.split("-")[1].replace(/\./g,"-");';
					$content_tmpl .= '$.ajax({url:"'.url("coupon/get_coupon/receive").'",data:{id:id , start_time:start_time, end_time:end_time},type:"GET",dataType:"json",';
					$content_tmpl .= 'success:function(ret){if(ret.status == 1) {';
					$content_tmpl .= '$.tips({icon:"success",content:ret.message,callback:function() {window.location.href=window.location.href;}});	} else {';
					$content_tmpl .= '$.tips({icon:"error",content:ret.message,callback:function() {return false;}});	}return false;';
					$content_tmpl .= '}})})';
					$content_tmpl .= '</script>';
					break;
				default:
					break;
			}
		}
		$compile_tmpl .= '<div class="mui-content">'.$content_tmpl.'</div>';
		$compile_tmpl .= '<footer class="footer"><div class="text-gray mui-text-center copy-text">';
		$compile_tmpl .= SITE_AUTHORIZE == 0? COPYRIGHT:"";
		$compile_tmpl .= '</div></footer>';
		$compile_tmpl .= '<script>$("[name=form_search]").submit(function(){if($("[type=search]").val() == ""){return false;}});</script>';
		$template = preg_replace('/<body.*?>(.*?)<\/body>/is',$compile_tmpl,$template);
		return $template;
	}
}