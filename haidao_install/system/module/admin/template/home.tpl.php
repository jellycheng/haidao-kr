<?php include template('header','admin');?>
	<?php
		/*通知*/
		$this->load->service('admin/cloud')->api_product_notify();
		$product_notify = cache('product_notify');
	?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">관리자메인</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<?php if(!empty($product_notify)):?>
			<div class="warn-info border bg-white margin-top padding-lr">
				<i class="warn-info-ico ico_warn margin-right"></i>
				<div id="FontScroll" style="height: 40px;overflow: hidden;">
					<ul>
						<?php foreach($product_notify as $k =>$v):?>
							<li><?php echo $v?></li>
						<?php endforeach;?>
					</ul>
				</div>
				<a href="javascript:;" class="close text-large fr" style="margin-top:-40px ;">×</a>
			</div>
			<?php endif;?>
			<div class="margin-top">
				<div class="fl w50 padding-small-right">
					<table cellpadding="0" cellspacing="0" class="border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">처리사항</th>
							</tr>
							<tr class="border">
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">주문알림</th>
											</tr>
											<tr class="text-lh-big">
												<td>입금대기중 상품：<a href="<?php echo url('order/admin_order/index',array('type' => 1)) ?>" title="点击去查看"><b class="text-main" data-id="pay">0</b></a></td>
												<td>확인대기중 상품：<a href="<?php echo url('order/admin_order/index',array('type' => 2)) ?>" title="点击去查看"><b class="text-main" data-id="confirm">0</b></a></td>
												<td>배송대기중 상품：<a href="<?php echo url('order/admin_order/index',array('type' => 3)) ?>" title="点击去查看"><b class="text-main" data-id="delivery">0</b></a></td>
											</tr>
											<tr class="text-lh-big">
												<td>평가대기중 상품：<b class="text-main" data-id="load_comment">0</b></td>
												<td>반품신청 대기중：<a href="<?php echo url('order/admin_server/index_return',array('type' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="load_return">0</b></a></td>
												<td>환불신청 대기중：<a href="<?php echo url('order/admin_server/index_refund',array('type' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="load_refund">0</b></a></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr class="border">
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">상품관리</th>
											</tr>
											<tr class="text-lh-big">
												<td>판매중인 상품：<b class="text-main" data-id="goods_in_sales">0</b></td>
												<td>진열대기중인 상품：<a href="<?php echo url('goods/admin/index',array('label' => 2)) ?>" title="点击去查看"><b class="text-main" data-id="goods_load_online">0</b></a></td>
												<td>재고경고중 상품：<a href="<?php echo url('goods/admin/index',array('label' => 3)) ?>" title="点击去查看"><b class="text-main" data-id="goods_number_warning">0</b></a></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">정보관리</th>
											</tr>
											<tr class="text-lh-big">
												<td>처리중인 문의：<a href="<?php echo url('goods/goods_consult/index',array('status' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="consult_load_do">0</b></a></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">쇼핑몰정보</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">완료된 주문 총건수</span>
									<a href="<?php echo url('order/admin_order/index',array('type' => 5)) ?>" title="点击去查看"><span class="fr" data-id="finish">0</span></a>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">모든 가입회원수</span>
									<a href="<?php echo url('member/member/index') ?>" title="点击去查看"><span class="fr" data-id="member_total">0</span></a>
								</td>
							</tr>
						</tbody>
					</table>
                    <table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">개발팀</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">总策划兼产品经理&emsp;</span>
									<span class="margin-large-left fl">董&emsp;浩</span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">产品设计与研发团队</span>
									<span class="margin-large-left fl">夏雪强&emsp;李春林&emsp;孔智翱&emsp;王小龙&emsp;饶家伟&emsp;秦秀荣</span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">번역</span>
									<span class="margin-large-left fl">한결ITS</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="fl w50 padding-small-left">
					<table cellpadding="0" cellspacing="0" class="border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">자금관리</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left to本月day-sales padding-big line-height-40">
									<span class="fl">금일 판매금액</span>
									<span class="margin-left text-big fl">￥<em class="h2 fr" data-id="today-amount">0.00</em></span>
									<span class="text-main fr">인평균단가：￥ <em data-id="today-average">0.00</em>
									</span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">이달 판매금액</span>
									<span class="margin-left fl">￥ <em data-id="month-amount">0.00</em></span>
									<span class="fr">월별통계</span>
								</td>
							</tr>
							<tr>
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">금년 판매금액</span>
									<span class="margin-left fl">￥ <em data-id="year-amount">0.00</em></span>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">시스템정보</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">시스템버전</span>
									<span class="fr">쇼핑몰&nbsp;v<?php echo HD_VERSION ?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">서버 및 PHP</span>
									<span class="fr"><?php echo php_uname('s');?>/<?php echo  PHP_VERSION;?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">서버 프로그램</span>
									<span class="fr"><?php echo php_uname('s');?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">데이터정보</span>
									<span class="fr">MySQL&nbsp;<?php echo mysql_get_server_info();?>/데이터크기&nbsp;<em data-id="dbsize">0</em>M</span>
								</td>
							</tr>
						</tbody>
					</table>
                    <table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">앱 센터</th>
							</tr>
							<tr>
								<td>
									<div class="text-left today-sales layout border-top border-white fl" style="padding:0 20px;height:65px;line-height:64px;background-color:#fbfbfb;">
                                        <span class="fl">현재 <b class="text-main">0</b>개의 업데이트 가능한 앱이 있습니다.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- <a class="text-main" href="">详情</a> --></span>
                                    </div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
<script>
	$('#FontScroll').FontScroll({time: 3000,num: 1});
	/* ajax加载统计 */
	$.ajax({
		url: "<?php echo url('statistics/order/ajax_home') ?>",
		type: 'get',
		dataType: 'json',
		success: function(ret) {
			// 订单数据
			if (ret.orders) {
				$.each(ret.orders,function(k, v) {
					$("[data-id='"+ k +"']").text(v);
				});
			}
			// 商品数据
			if (ret.goods) {
				$.each(ret.goods,function(k, v) {
					$("[data-id='"+ k +"']").text(v);
				});
			}
			// 待处理咨询
			$("[data-id='consult_load_do']").text(ret.consult_load_do);
			// 注册人数
			$("[data-id='member_total']").text(ret.member_total);
			// 资金管理
			$("[data-id='today-amount']").text(ret.sales.today.amount);
			$("[data-id='today-average']").text(ret.sales.today.average);
			$("[data-id='month-amount']").text(ret.sales.month.amount);
			$("[data-id='year-amount']").text(ret.sales.year.amount);
			/* 数据库大小 */
			$("[data-id='dbsize']").text(ret.dbsize[0].db_length);
		},
		error: function(errorMsg) {
			message("请求数据失败，请稍后再试！");
		}
	});
</script>
