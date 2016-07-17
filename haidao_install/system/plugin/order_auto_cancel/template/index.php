<?php include template('header','admin'); ?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
<div class="fixed-nav layout">
	<ul>
		<li class="first">订单自动取消管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
			<p>- 主要用于在线付款订单，当订单下单超过设置的时间[单位：分钟]，订单自动取消，节约商城管理人员时间成本</p>
		</div>
	</div>
	<div class="hr-gray"></div>
	<form class="addfrom" name="form1" id="form1" action="" method="post">
	<dl class="gzzt clearfix mt10">
		<dd>
			<div class="time fl">
					<?php echo Form::input('radio','status',$info["status"],'是否开启：','开启后，未付款的在线支付订单将在设置时间后自动取消',array('items'=>array('关闭','开启'),'colspan'=>'2')); ?>
					<?php echo Form::input('text', 'time', $info["time"] ? $info["time"] : 15, '设置取消时间：', '订单取消时间[单位：分钟]'); ?>
			</div>
		</dd>
	</dl>
		<div class="padding">
			<input type="submit" class="button bg-main" value="设置" />
			<a class="button margin-left bg-gray" href="">返回</a>
		</div>
	</form>
</div>
<script>
$(window).load(function(){
	})
</script>