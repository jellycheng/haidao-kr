<?php include template('header','admin'); ?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
<div class="fixed-nav layout">
	<ul>
		<li class="first">商品限购管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<div class="tips margin-tb">
		<div class="tips-info border">
			<h6>温馨提示</h6>
			<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
		</div>
		<div class="tips-txt padding-small-top layout">
			<p>- 主要用于指定商品限购插件，支持指定每多少时间限购多少数量</p>
		</div>
	</div>
	<div class="hr-gray"></div>
	<form class="addfrom" name="form1" id="form1" action="" method="post">
	<dl class="gzzt clearfix mt10" style="margin-top:10px;">
		<dd>
			<div class="time fl">
				<strong>时间范围：</strong>
				<?php echo Form::input('calendar', 'start_time', $info['start_time'] ? date('Y-m-d H:i:s',$info['start_time']) : date('Y-m-d H:i:s'), '开始时间：', '开始时间'); ?>
				<?php echo Form::input('calendar', 'end_time', $info['end_time'] ? date('Y-m-d H:i:s',$info['end_time']) : date('Y-m-d H:i:s',strtotime('+ 1 month')), '结束时间：', '结束时间'); ?>
			</div>
		</dd>
	</dl>
     <div style="margin-top: 10px;">
        	<strong>请选择限购的商品：</strong>
        </div>
        <div class="padding">
				<div class="table-work border margin-tb">
					<div class="border border-white tw-wrap">
						<a class="choose-goods" href="javascript:;"><i class="ico_add"></i>选择商品</a>
						<div class="spacer-gray"></div>
						<a class="all_choose" href="javascript:;">批量设置</a>
						<div class="spacer-gray"></div>
					</div>
				</div>
				<div class="table resize-table border high-table clearfix">
					<div class="tr border-none" style="visibility: visible">
						<div class="th w30" data-width="30">
							<span class="td-con">商品名称</span>
						</div>
						<div class="th w20" data-width="20">
							<span class="td-con">价格</span>
						</div>
						<div class="th w15" data-width="15">
							<span class="td-con">库存</span>
						</div>
						<div class="th w20" data-width="20">
							<span class="td-con">限购数量</span>
						</div>
						<div class="th w15" data-width="15">
							<span class="td-con">操作</span>
						</div>
					</div>
					<script id="prom_template" type="text/html">
					<%for(var item in templateData){%>
            		<%item = templateData[item]%>
					<div class="tr sku_lists" style="visibility: visible" data-skuid="<%=item['id']%>">
						<div class="td w30">
							<div class="td-con td-pic text-left">
								<div class="pic"><img src="<%=item['pic']%>" /></div>
								<div class="title">
									<p class="text-ellipsis padding-small-left"><%=item['title']%></p>
								</div>
							</div>
						</div>
						<div class="td w20 price">
							<div class="td-con" data-id="<%=item['id']%>" data-price="<%=item['price']%>" data-nums="<%=item['nums']%>" data-number="<%=item['number']%>"><input name="sku_price" type="hidden" value="<%=item['price']%>">￥<%=item['price']%></div>
						</div>
						<div class="td w15 ">
							<div class="td-con"><%=item['number']%></div>
						</div>
						<div class="td w20">
							<div class="td-con">
								<input style="margin-top:22px;" class="input double-click-edit text-ellipsis text-center nums" name="sku_ids[<%=item['id']%>]" type="text" value="<%=item['nums']%>" data-reset="false" />
							</div>
						</div>
						<div class="td w15">
							<div class="td-con"><a class="remove-tr" href="">移除</a></div>
						</div>
					</div>
					<% }%>
					</script>
				</div>
			</div>
		<div class="padding">
			<input type="submit" class="button bg-main" value="设置" />
			<a class="button margin-left bg-gray" href="">返回</a>
		</div>
	</form>
</div>
<script>
$(window).load(function(){
	<?php if (isset($lists)): ?>
	$('.table .tr:gt(0)').remove();
	var info = <?php echo json_encode($lists) ?> ;
	var goodsRowHtml = template('prom_template', {'templateData': info});
	$('.table .tr').after(goodsRowHtml);
	<?php endif ?>

	//移除
	var removeids = {};
	$(".remove-tr").live('click',function(){
		var sku_id = $(this).parents(".tr").data('skuid');
		removeids[sku_id] = {'removeid':sku_id};
		$(this).parents(".tr").remove();
		return false;
	});
	$(".choose-goods").live('click',function(){
			var data = {};
			$('.sku_lists').each(function(i,item){
				var params = {
					id : $(this).find(".price div").attr('data-id'),
					title : $(this).find(".title p").html(),
					pic : $(this).find(".pic img").attr('src'),
					price : $(this).find(".price div").attr('data-price'),
					nums : $(this).find(".price div").attr('data-nums'),
					number : $(this).find(".price div").attr('data-number')
				}
				data[$(this).find(".price div").attr('data-id')] = params;
			})
			top.dialog({
				url: '<?php echo url('goods/sku/select', array('multiple' => 1))?>',
				title: '加载中...',
				removeids:removeids,
				selected:data,
				width: 980,
				onclose: function () {
					console.log(this.returnValue);
					if(this.returnValue){
						$('.table .tr:gt(0)').remove();
						var goodsRowHtml = template('prom_template', {'templateData': this.returnValue});
						$('.table .tr').after(goodsRowHtml);
					}
				}
			})
			.showModal();
		})
	$(".all_choose").live('click',function(){
		var data = {};
		$('.sku_lists').each(function(i,item){
			var params = {
				id : $(this).find(".price div").attr('data-id')
			}
			data[$(this).find(".price div").attr('data-id')] = params;
		})
		top.dialog({
			title: '批量设置',
		    width: 350,
		    okValue: "生成",
		    content: '<div class="form-box border-bottom-none order-eidt-popup clearfix"><div class="form-group"><span class="label"><b>为限购商品批量设置限购数量</b></span><div class="box"><input class="input" type="text" value="+0" id="nums"></div></div></div><p style="font-size:12px;padding:10px;">小提示：此处修改的值将对所有商品限购数量进行加减修改如:+10 -5必须是整数</p>',
		    ok: function () {
		    	$('.nums').each(function(i,item){
					var num = $(this).val() ? $(this).val() : 0;
					var new_num = parseInt(num) + parseInt(window.parent.$('#nums').val());
					new_num = new_num > 0 ? new_num : 0;
					$(this).val(new_num);
				})
		        
		    },
		    cancelValue: "取消",
		    cancel: function(){

		    }
		})
		.showModal();
	})
})
</script>