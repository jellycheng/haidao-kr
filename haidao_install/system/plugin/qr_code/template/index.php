<?php include template('header','admin'); ?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
<div class="fixed-nav layout">
	<ul>
		<li class="first">商品二维码管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
			<p>- 执行此操作可以在前台商品详情页开启二维码图片</p>
		</div>
	</div>
	<div class="hr-gray"></div>
	<form class="addfrom" name="form1" id="form1" action="" method="post">
	<div class="youhui mt10">
		<ul>
            <li class="borm">
				<?php echo Form::input('enabled', 'status', !is_null($partook['status']) ? $partook['status'] : 1, '是否开启：', '设置是否开启显示商品二维码'); ?>
			</li>
		</ul>
	</div>
		<div  style="padding-top:100px">
			<input type="submit" class="button bg-main" value="保存" />
			<a class="button margin-left bg-gray" href="">返回</a>
		</div>
	</form>
</div>
