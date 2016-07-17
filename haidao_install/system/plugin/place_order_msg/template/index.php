<?php include template('header','admin'); ?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
<style type="text/css">
	.operate-info input{width: 140px !important;}
	.notice {
	    padding: 0 20px;
	    width: 100%;
	    line-height: 35px;
	    background-color: #eff7fe;
	}
	.form-group {
	    padding-left: 20px;
	    padding-right: 20px;
	    border: 0;
	}
	.form-group {
	    position: relative;
	    float: left;
	    width: 100%;
	}
	.form-group {
	    border-bottom: 1px dotted #ccc;
	}
	.form-group .select-wrap {
	    padding: 5px 0;
	}
	.form-group .select-wrap {
	    display: inline-block;
	    margin-right: 20px;
	    line-height: 26px;
	}
</style>
<div class="fixed-nav layout">
	<ul>
		<li class="first">插件管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
		<li><a class="current" href="javascript:;"></a></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<form class="addfrom" name="form1" id="form1" action="" method="post">
	<div class="form-box clearfix">
		<?php echo Form::input('radio','msg_status',$info["msg_status"],'是否开启短信通知：','开启后用户下单，管理员可以接受短信通知',array('items'=>array('关闭','开启'),'colspan'=>'2')); ?>
	</div>
	<div class="form-box clearfix">
	<?php echo Form::input('textarea','phone', $info['phone'],'短信通知号码','填写用户下单时系统通知的短信号码，如通知多个号码，每行一个'); ?>
	</div>
	<!-- <div class="form-box clearfix">
		<div class="form-group">
			<div class="wrap border border-sub bg-white clearfix">
			<p class="notice">您正在编辑 <em id="content-label" class="text-main">下单短信通知</em> 通知模板 <?php if($sms_num['code'] != 200 && $sms_num['result'] === false){?><span class="fr">您尚未绑定云平台，无法使用短信通知。<a href="<?php echo url('admin/cloud/index')?>" class="text-main">立即绑定</a></span>
			<?php }else{?>
			<span class="fr">您的短信剩余条数：<em class="text-main"><?php echo $sms_num['result'];?></em></span>
			<?php }?></p>

			
				<div class="form-group">
					<span class="label"></span>
					<div class="">
						<?php foreach($admin_msg as $sk => $sv):?>
						<label class="select-wrap">
							<?php
							$checked = $sv['id'] == $info['template_id'] ? 'checked=checked' : '' ;
							?>
							<input type="radio"  value="<?php echo $sv['id']?>" name="template_id" class="select-btn" <?php echo $checked?>><?php echo $sv['content']?>
						</label><br>
						<?php endforeach;?>
					</div>
				</div>

			</div>
		</div>
	</div> -->
	<div class="padding">
		<input type="submit" class="button bg-main" value="设置" />
		<a class="button margin-left bg-gray" href="">返回</a>
	</div>
</form>

<script type="text/javascript">
	function delNewAttr(self){
		if (!confirm("确认要删除么？")) {
                return false;
            }
		$(self).parent().parent('.tr').remove();
	}
	$(function(){
		$('.resize-table').resizableColumns();
		$("[data-event='add_phone']").live('click', function(){
			var _indent = $("[data-model='phones']").find('.tr').length;
			var _html = template('phones_template', {"i" : (_indent)});
			$(this).parents("div.spec-add-button").before(_html);
			
		})

		$("select[name^=phones]").live("change", function(){
			var _val = $(this).val();
			var _id = $(this).parents(".tr[data-id]").data('id');
			var _html = template('phone_' + _val, {"i" : _id});
			$(this).parents(".tr[data-id]").find("div[data-model='phone']").html(_html);
		})


		
	})		
</script>
<script id="phones_template" type="text/html">
<div class="tr" data-id="<%=i%>" style="visibility: visible;">
	<div class="td w65">
		<div class="td-con operate-info text-left" data-model="phone">
			<input class="input" type="text" name="phone[<%=i%>]"/>
		</div>
	</div>
	<div class="td w15 padding-left" style="width: 67px;"><a href="javascript:;" data-event="del_phone" onclick="delNewAttr(this)">删除</a></div>
</div>
</script>
	</body>
</html>
