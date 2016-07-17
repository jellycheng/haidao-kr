<?php
class plugin_goods_buy_limit extends plugin{

	/* 商品限购 商品详情 */
	//返回用户对当前sku_id可购数量
	public function detail_goods_num_info(){
		$member = model('member/member','service')->init();
		$info = cache('goods_buy_limit');
		//判断当前时间是否在限购时间段内
		if(time() < (int)$info['start_time'] || time() > $info['end_time']) return false;
		//判断当前购买商品是否在限购商品中
		$sku_id = $_GET['sku_id'];
		$sku_ids = array_keys($info['sku_ids']);
		if(empty($sku_ids)) return false;
		if(!in_array($sku_id,$sku_ids)) return false;
		//判断当前用户是否登录
		$buyer_id = $member['id'];
		if((int)$buyer_id < 1) return false;
		//当前可购买
		$can_buy = (int)$info['sku_ids'][$sku_id];
		$sqlmap = array();
		$cart_nums = $order_nums = 0;
		$sqlmap['buyer_id'] = $buyer_id;
		$sqlmap['sku_id'] = $sku_id;
		//获取当前用户当前商品购物车中的数量
		$cart_nums = model('cart')->where($sqlmap)->getField('nums');
		$cart_nums = $cart_nums ? (int)$cart_nums : 0;
		//获取当前用户当前商品今天已购买的数量
		$sqlmap['dateline'] = array('between',array(strtotime(date('Y-m-d', time())),time()));
		$order_nums = model('order_sku')->where($sqlmap)->sum('buy_nums');
		$order_nums = $order_nums ? (int)$order_nums : 0;
		//获取当前用户今天已购和购物车中当前商品的数量
		$total_nums = $cart_nums + $order_nums;
		//判断当前用户今天还可以购买当前商品的数量
		$can_buy = (int)$info['sku_ids'][$sku_id] - $total_nums;
		$can_buy = ($can_buy < 0) ? 0 : $can_buy;
		return '<div class="tishi" style="display:none;width:111px;height:30px;border:1px solid #dddddd;line-height:30px;font-size:12px;text-align:center;position:absolute;top:-16px;color:red;background-color:#feffe6;">
					<u>可购'.$can_buy.'件</u>
				</div>
				<script type="text/javascript">
					$(function(){
						var can_buy = '.$can_buy.';
						$("[data-event=buy_now],[data-event=cart_add]").bind("click",function(){
							var num = $(".adjust-input").val();
							if(parseInt(num) > parseInt(can_buy) && can_buy != null){
								$(".tishi").show();
								return false;
							}
						})
						$(".tishi").live("click",function(){
							$(this).hide();
						});
					})
				</script>';
	}

	/* 商品限购 购物车 */
	//返回用户对限购商品的可购数量
	public function cart_settlement_footer(){
		$member = model('member/member','service')->init();
		$carts = $this->load->Librarys('View')->fetch('carts');
		$sku_list = $carts['skus'][0]['sku_list'];
		$cart_sku_ids = array();
		foreach ($sku_list as $k => $goods) {
			$cart_sku_ids[$goods['sku_id']] = $goods['number'];
		}
		$info = cache('goods_buy_limit');
		$cache_sku_ids = array_keys($info['sku_ids']);
		//判断购物车中是否有限购商品
		$skuids = array();
		foreach ($cart_sku_ids as $sku_id => $number) {
			if(in_array($sku_id,$cache_sku_ids)){
				$skuids[$sku_id] = $number;
			}
		}
		if(empty($skuids)) return false;
		//判断是否登录
		$buyer_id = $member['id'];
		if((int)$buyer_id < 1) return false;
		$sku_ids = $sqlmap = array();
		$cart_nums = $order_nums = $total_nums = 0;
		$sqlmap['buyer_id'] = $buyer_id;
		foreach ($skuids as $sku_id => $number) {
			$sqlmap['sku_id'] = $sku_id;
			//获取当前用户当前商品今天已购买的数量
			$sqlmap['dateline'] = array('between',array(strtotime(date('Y-m-d', time())),time()));
			$order_nums = (int) model('order_sku')->where($sqlmap)->sum('buy_nums');
			//判断当前用户今天还可以购买当前商品的数量
			$can_buy = (int)$info['sku_ids'][$sku_id] - $order_nums;
			$can_buy = ($can_buy < 0) ? 0 : $can_buy;
			$sku_ids[$sku_id] = $can_buy;
		}
		$sku_info = json_encode($sku_ids);//返回ID=>数量
		$sku_id_info = json_encode(array_keys($sku_ids));//只返回ID
		return '<style type="text/css">
					.tishi{
						display:block;
						width:78px;
						height:30px;
						border:1px solid #dddddd;
						line-height:30px;
						font-size:12px;
						text-align:center;
						position:relative;
						margin:28px 0 0 49px;
						color:red;
						background-color:#feffe6;
					}
				</style>
				<script type="text/javascript">
					var sku_ids = '.$sku_info.';
					var ids = '.$sku_id_info.';
					$("[data-id=settlement]").bind("click",function(){
						var flog = 0;
						$(".tr").each(function(i,item){
							var sku_id = $(this).data("skuid");
							if(ids.indexOf(sku_id) > -1){
								var _num = $(this).find(".adjust-input").val();
								var can_buy_nums = sku_ids[sku_id];
								var _html = "<div class=tishi><u>可购<span>"+can_buy_nums+"</span>件</u>";
								if(parseInt(_num) > parseInt(can_buy_nums)){
									flog += 1;
									if(!$(this).find(".tishi").hasClass("open")){
										$(this).find(".cart-nums").append(_html);
									}else{
										$(this).find(".tishi").show();
									}
									$(this).find(".tishi").addClass("open");
								}else{
									flog += 0;
								}
							}
						})
						if(flog > 0) return false;
					})
					$(".tishi u").live("click",function(){
						if($(this).parents(".tishi").hasClass("open")){
							$(this).parents(".tishi").hide();
						}
					});
				</script>';
	}

	/* 商品限购 确认订单 */
	//返回用户对限购商品的可购数量
	public function settlement_info(){
		$member = model('member/member','service')->init();
		$buyer_id = $member['id'];
		$info = cache('goods_buy_limit');
		$cache_sku_ids = array_keys($info['sku_ids']);
		//判断是否登录
		if((int)$buyer_id < 1) return false;
		$skuids = explode(';', $_GET['skuids']);
		if(empty($skuids)) return false;
		$sku_ids = array();
		if(count($skuids) > 1){//购买多个商品时，去购物车取出购买商品数量，将数组组装成sku_id=>number
			foreach ($skuids as $key => $sku_id) {
				if(!is_numeric($sku_id) && (int) $sku_id < 1){
					unset($skuids[$key]);
				}
			}
			foreach ($skuids as $key => $sku_id) {
				$sku_ids[$sku_id] = model('cart')->where(array('sku_id'=>$sku_id,'buyer_id'=>$buyer_id))->getField('nums');
			}
		}else{//购买单个商品时，将数组组装成sku_id=>number
			$skuids = explode(',',$skuids[0]);
			$sku_ids[$skuids[0]] = $skuids[1];
		}
		//判断购物车中是否有限购商品，没有则不执行，反之则把有限购的ID存入数组
		$id_list = array();
		foreach ($sku_ids as $sku_id => $number) {
			if(in_array($sku_id,$cache_sku_ids)){
				$id_list[$sku_id] = $number;
			}
		}
		if(empty($id_list)) return false;
		$sqlmap = $sku_info = array();
		$sqlmap['buyer_id'] = $buyer_id;
		foreach ($id_list as $sku_id => $number) {
			$sqlmap['sku_id'] = $sku_id;
			//获取当前用户当前商品今天已购买的数量
			$sqlmap['dateline'] = array('between',array(strtotime(date('Y-m-d', time())),time()));
			$order_nums = (int) model('order_sku')->where($sqlmap)->sum('buy_nums');
			//判断当前用户今天还可以购买当前商品的数量
			$can_buy = (int)$info['sku_ids'][$sku_id] - $order_nums;
			$can_buy = ($can_buy < 0) ? 0 : $can_buy;
			$sku_info[$sku_id] = $can_buy;
		}
		$sku_list = json_encode($sku_info);//返回ID=>数量
		$sku_id_list = json_encode(array_keys($sku_info));//只返回ID
		return '<style type="text/css">
					.tishi{
						display:block;
						width:78px;
						height:30px;
						border:1px solid #dddddd;
						line-height:30px;
						font-size:12px;
						text-align:center;
						color:red;
						background-color:#feffe6;
					}
				</style>
				<script type="text/javascript">
					var sku_ids = '.$sku_list.';
					var ids = '.$sku_id_list.';
					var flog = 0;
					$(".sku").each(function(i,item){
						var sku_id = $(this).data("skuid");
						if(ids.indexOf(sku_id) > -1){
							var _num = $(this).find(".cart-nums span").text();
							_num = _num.replace(/[^0-9]/ig,"");
							var can_buy_nums = sku_ids[sku_id];
							var _html = "<div id=tishi class=tishi><u>可购<span>"+can_buy_nums+"</span>件</u>";
							if(parseInt(_num) > parseInt(can_buy_nums)){
								flog += 1;
								if(!$(this).find(".tishi").hasClass("open")){
									$(this).find(".cart-nums").append(_html);
								}else{
									$(this).find(".tishi").show();
								}
								$(this).find(".tishi").addClass("open");
							}else{
								flog += 0;
							}
						}
					})
					if(flog > 0){
						window.onload = function(){
							$(".cart-btn").attr("href","#tishi").removeAttr("id").addClass("gray-btn").find("b").html("存在限购商品");
						}
					}
					$(".tishi u").live("click",function(){
						if($(this).parents(".tishi").hasClass("open")){
							$(this).parents(".tishi").hide();
						}
					});
				</script>';
	}
}