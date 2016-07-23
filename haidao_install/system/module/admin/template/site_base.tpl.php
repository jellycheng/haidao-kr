<?php include template('header','admin');?>
<div class="fixed-nav layout">
    <ul>
        <li class="first">쇼핑몰설정<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">쇼핑몰정보</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">기본설정</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">구매설정</a></li>
		<li class="fixed-nav-tab"><a href="javascript:;">반품설정</a></li>
		<li class="fixed-nav-tab"><a href="javascript:;">택배설정</a></li>
    </ul>
    <div class="hr-gray"></div>
</div>
<form action="" method="POST" enctype="multipart/form-data">
<div class="content padding-big have-fixed-nav">
    <div class="content-tabs">
        <div class="form-box clearfix">
		<?php echo Form::input('text', 'site_name', $setting['site_name'], '쇼핑몰이름：', '쇼핑몰이름，웹브라우저창 제목위치에 노출됩니다'); ?>
		<?php echo Form::input('text', 'com_name', $setting['com_name'], '회사이름：', '사이트이름，페이지하단 연락처부분에 표시됩니다'); ?>
		<?php echo Form::input('text', 'site_url', $setting['site_url'], '쇼핑몰URL：', '쇼핑몰 URL，페이지하에 표기됩니다，http://로 시작'); ?>
<!--		--><?php //echo Form::input('text', 'icp', $setting['icp'], '쇼핑몰허가 정보코드(备案信息)：', '페이지하단에 ICP허가정보가 표기됩니다，판매허가된 경우，입력란에 허가번호를 입력하세요. 없으면 비워주세요'); ?>
		<?php echo Form::input('radio', 'site_isclosed', $setting['site_isclosed'], '쇼핑몰 운영상태：', '잠시 쇼핑몰 운영정지，방문자의 사이트 방문을 제한합니다，관리자방문에는 영향주지 않습니다', array('items' => array('1'=>'열기', '0'=>'닫기'), 'colspan' => 2,)); ?>
	    <?php echo Form::input('textarea', 'site_closedreason', $setting['site_closedreason'], '사이트 임시중지 사유를 입력하세요.'); ?>
		 <?php echo Form::input('textarea', 'site_statice', $setting['site_statice'], '쇼핑몰 제3자통계코드：','페이지 하단에 제3자통계코드를 표기합니다'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
			<?php echo Form::input('file', 'site_logo',$setting['site_logo'], '쇼핑몰LOGO：','쇼핑몰 로고이미지, 이 이미지는 회원센터로고와 다르게 사용됩니다.');?>
        	<?php echo Form::input('select', 'timeoffset',$setting['timeoffset'], '시스템시간：', '현지시간과  GMT의 시차', array('items' => array("-12"=>"(GMT -12:00) 埃尼威托克岛, 夸贾林..","-11"=>"(GMT -11:00) 中途岛, 萨摩亚群岛..","-10"=>"(GMT -10:00) 夏威夷","-9"=>"(GMT -09:00) 阿拉斯加","-8"=>"(GMT -08:00) 太平洋时间(美国和加拿..","-7"=>"(GMT -07:00) 山区时间(美国和加拿大..","-6"=>"(GMT -06:00) 中部时间(美国和加拿大..","-5"=>"(GMT -05:00) 东部时间(美国和加拿大..","-4"=>"(GMT -04:00) 大西洋时间(加拿大), ..","-3.5"=>"(GMT -03:30) 纽芬兰","-3"=>"(GMT -03:00) 巴西利亚, 布宜诺斯艾..","-2"=>"(GMT -02:00) 中大西洋, 阿森松群岛,..","-1"=>"(GMT -01:00) 亚速群岛, 佛得角群岛 ..","0"=>"(GMT) 卡萨布兰卡, 都柏林, 爱丁堡, ..","1"=>"(GMT +01:00) 柏林, 布鲁塞尔, 哥本..","2"=>"(GMT +02:00) 赫尔辛基, 加里宁格勒,..","3"=>"(GMT +03:00) 巴格达, 利雅得, 莫斯..","3.5"=>"(GMT +03:30) 德黑兰","4"=>"(GMT +04:00) 阿布扎比, 巴库, 马斯..","4.5"=>"(GMT +04:30) 坎布尔","5"=>"(GMT +05:00) 叶卡特琳堡, 伊斯兰堡,..","5.5"=>"(GMT +05:30) 孟买, 加尔各答, 马德..","5.75"=>"(GMT +05:45) 加德满都","6"=>"(GMT +06:00) 阿拉木图, 科伦坡, 达..","6.5"=>"(GMT +06:30) 仰光","7"=>"(GMT +07:00) 曼谷, 河内, 雅加达..","8"=>"(GMT +08:00) 북경, 홍콩..","9"=>"(GMT +09:00) 서울, 오사카, 동..","9.5"=>"(GMT +09:30) 阿德莱德, 达尔文..","10"=>"(GMT +10:00) 堪培拉, 关岛, 墨尔本,..","11"=>"(GMT +11:00) 马加丹, 新喀里多尼亚,..","12"=>"(GMT +12:00) 奥克兰, 惠灵顿, 斐济,.."))); ?>
			<?php echo Form::input('text', 'exp_rate', $setting['exp_rate'], '경험치 획득비율：', '소비1원 이면 얻을 수 있는 경험치수량, 0이면 닫기'); ?>
			<?php echo Form::input('textarea', 'hot_seach', $setting['hot_seach'], '인기검색어：','인기검색어를 입력하세요, 쇼핑몰에 표기 될 검색어 입니다. 한 행에 한개 검색어'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix" id="buy">
        	<?php echo Form::input('select', 'pay_type',$setting['pay_type'], '결제유형설정：', '결제시 지원할 결제유형을 선택하세요, 기본설정:"온라인결제 + 착불"
', array('items' => array(1 => '온라인결제 + 착불', 2 => '온라인결제만', 3 => '착불만'))); ?>
			<?php echo Form::input('checkbox', 'pays[]', implode(',', unserialize($setting['pays'])), '지원되는 결제방식 선택：', '온라인결제시 지원되는 결제방식설정, 우선 결제시스템 설정에서 설치후 사용 가능합니다.', array('items' => $payment)); ?>
			<?php echo Form::input('select', 'cart_jump',$setting['cart_jump'], '장바구니 설정：', '“장바구니 담기”알림 설정。기본：구매성공 페이지로 넘어감', array('items' => array('구매성공 페이지로 넘어감', '장바구니 페이지로 넘어감', '현재 페이지상태에서 직접 장바구니추가'))); ?>
			<?php echo Form::input('select', 'stock_change',$setting['stock_change'], '재고설정：', '재고축소유형을 설정합니다，기본설정 : 상품을 주문하면 재고개수 감소
', array('items' => array('상품을 주문하면 재고개수 감소', '주무한 상품 발송 시 재고개수 감소'))); ?>
			<?php echo Form::input('radio', 'invoice_vat_enabled', $setting['invoice_vat_enabled'], '구매기록 표시여부：', '쇼팡몰 상품 상세페이지에 구매기록내역 노출여부를 설정합니다.', array('items' => array('1'=>'열기', '0'=>'닫기'), 'colspan' => 2,)); ?>
			<?php echo Form::input('radio', 'balance_pay', $setting['balance_pay'], '잔액결제기능 사용여부：', '잔액결제기능 사용여부를 설정합니다，기능사용 후,잔액 지원되는 충전결제방식을 설정하세요', array('items' => array('1'=>'열기', '0'=>'닫기'), 'colspan' => 2,)); ?>
			<?php
			if(isset($payment['alipay_escow'])) {
				$payment['alipay_escow'] = $payment['alipay_escow'].'(미지원)';
			}
			?>
			<?php echo Form::input('checkbox', 'balance_deposit[]', unserialize($setting['balance_deposit']), '잔액충전 가능한 결제방식을 선택하세요*：', '잔액충전이 지원되는 결제방식을 설정하세요，우선 결제시스템설정에서 사용해 주십시오', array('items' => $payment, 'colspan' => 1, 'disabled' => 'alipay_escow')); ?>
			<?php echo Form::input('radio', 'invoice_enabled', $setting['invoice_enabled'], '영수증출력기능 사용여부：', '영수증출력기능 사용여부를 설정합니다.', array('items' => array('1'=>'열기', '0'=>'닫기'), 'colspan' => 2,)); ?>
        </div>
        <div class="form-box clearfix for-invoice">
			<?php echo Form::input('text', 'invoice_tax', $setting['invoice_tax'], '영수증 세율：', '영수증 세율설정，단위:%'); ?>
			<?php echo Form::input('textarea', 'invoice_content', implode("\r\n",unserialize($setting['invoice_content'])), '영수증내용설정：','고객이 영수증수요시 선택가능한 내용, 한줄에 한개내용표시, 예：사무용품'); ?>
        </div>
    </div>
	<div class="content-tabs hidden">
        <div class="form-box clearfix">
		<?php echo Form::input('text', 'seller_address', $setting['seller_address'], '반품받는 주소：', '판매자주소, 구매자가 반품정보입력 하는곳에 노출됨'); ?>
		<?php echo Form::input('text', 'seller_name', $setting['seller_name'], '수취인：', '판매자이름，구매자가 반품정보입력 하는곳에 노출됨'); ?>
		<?php echo Form::input('text', 'seller_mobile', $setting['seller_mobile'], '연락번호：', '판매자 연락번호，구매자가 반품정보입력 하는곳에 노출됨'); ?>
		<?php echo Form::input('text', 'seller_zipcode', $setting['seller_zipcode'], '우편번호：', '판매자 우편번호，구매자가 반품정보입력 하는곳에 노출됨'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
		<?php echo Form::input('text', 'sender_name', $setting['sender_name'], '배송자이름：', '배송자이름，택배운송장 배송자입력창에 나타남'); ?>
		<?php echo Form::input('text', 'sender_mobile', $setting['sender_mobile'], '배송자 연락번호：', '배송자 연락번호，택배운송장 배송자입력창에 나타남'); ?>
		<?php echo Form::input('text', 'sender_address', $setting['sender_address'], '배송자 주소：', '배송자 주소，택배운송장 배송자입력창에 나타남'); ?>
        </div>
    </div>
    <div class="padding">
        <input type="submit" class="button bg-main" value="저장" />
    </div>
</div>
</form>
</body>
</html>

<script>
	$(function(){
		/* 支付方式 */
		if ($("input[name=pay_type]").val() == '1' || $("input[name=pay_type]").val() == '2') {
			$("input[name=pay_type]").parents('.form-group').next().show();
		} else {
			$("input[name=pay_type]").parents('.form-group').next().hide();
		}
		$("input[name=pay_type]").change(function() {
			if ($(this).val() == '1' || $(this).val() == '2') {
				$("input[name=pay_type]").parents('.form-group').next().show();
			} else {
				$("input[name=pay_type]").parents('.form-group').next().hide();
			}
		});

		$.each($("input[name=site_isclosed]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-group").next().show();
				}else{
					$(this).parents(".form-group").next().hide();
				}
			}
		})
		$.each($("input[name=balance_pay]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-group").next().hide();
				}else{
					$(this).parents(".form-group").next().show();
				}
			}
		})
		$.each($("input[name=invoice_enabled]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-box").next().hide();
				}else{
					$(this).parents(".form-box").next().show();
				}
			}
		})
	})
	$("input[name=site_isclosed]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-group").next().slideDown(100);
		}else{
			$(this).parents(".form-group").next().slideUp(100);
		}
	})
	$("input[name=balance_pay]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-group").next().slideUp(100);
		}else{
			$(this).parents(".form-group").next().slideDown(100);
		}
	})
	$("input[name=invoice_enabled]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-box").next().slideUp(100);
		}else{
			$(this).parents(".form-box").next().slideDown(100);
		}
	})
</script>
