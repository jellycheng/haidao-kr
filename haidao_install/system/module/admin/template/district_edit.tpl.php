<?php include template('header','admin');?>
<form action="<?php echo url('edit') ?>" name="district_edit">
	<div class="form-box border-none clearfix padding-tb">
		<input type="hidden" name="id" value="<?php echo $r['id']; ?>"/>
		<?php if ($parent_pos): ?>
			<?php echo Form::input("text", 'parent_id', implode($parent_pos, ' > '), '上级地区', '', array("disabled" => "disabled")) ?>
		<?php endif ?>
		<?php echo Form::input('text', 'name', $r['name'], '地区名称', '', array(
			'datatype' => '*',
			'nullmsg' => '地址名称不能为空'
		)); ?>
		<?php echo Form::input('text', 'pinyin', $r['pinyin'], '地区拼音'); ?>
		<?php echo Form::input('text', 'zipcode', $r['zipcode'], '邮政编码', '', array(
			'ignore'   =>'ignore',
			'datatype' => 'zip',
			'errormsg' => '邮政编码格式错误'
		)); ?>
	</div>
	<div class="padding margin-big-top text-right ui-dialog-footer">
		<input type="submit" class="button bg-main" name="dosubmit" value="确定" />
		<input type="reset" class="button margin-left bg-gray" value="取消" />
	</div>
</form>
<script type="text/javascript">
$(function() {
	try {
		var dialog = top.dialog.get(window);
		dialog.title('地区编辑');
		dialog.reset();
	} catch (e) {
		return;
	}

	var district_edit = $("[name=district_edit]").Validform({
		ajaxPost:true,
		callback:function(ret) {
			dialog.title(ret.message);
			if(ret.status == 1) {
				setTimeout(function(){
					dialog.close(ret.message);
					dialog.remove();
				}, 1000);
			} else {
				return false;
			}
		}
	});

	$('[type=reset]').on('click', function() {
		dialog.close();
		return false;
	});
})</script>
</body>
</html>