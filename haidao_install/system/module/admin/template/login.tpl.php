<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>관리자로그인 - iNBAY.kr</title>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/haidao.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/admin.css" />
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/jquery-1.7.2.min.js" ></script>
		<!--<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.form.js" ></script>-->
	</head>
	<body>
		<div class="login-wrapper">
			<div class="left fl">
				<span class="logo"><img src="<?php echo __ROOT__;?>statics/images/login_logo.png" width="203px"/></span>
				<p class="margin-big-top">관리자 접속페이지 입니다.<br />쇼핑몰관리자, 상품관리자는 여기로 접속하시면 됩니다.</p>
			</div>
			<div class="right fr">
                <form action="<?php echo url('login');?>" onsubmit="return submit_check()" method="POST" data-layout="rank">
				<div class="form-box form-layout-rank border-bottom-none clearfix">
					<?php echo Form::input('text', 'username', '', '아이디 : ', ''); ?>
					<?php echo Form::input('password', 'password', '', '비밀번호 :'); ?>
				</div>
				<div class="login-btn margin-top">
                    <input type="submit" name="dosubmit" class="button bg-main" value="확인" />
				</div>
                </form>
			</div>
		</div>
		<script>
			$('.form-group:last-child').css({zIndex:"3"});
			function submit_check(){
				var $user=$("input[name=username]").val();
				var $pass=$("input[name=password]").val();
				if(!$user || !$pass){
					return false;
				}
			}
		</script>
	</body>
</html>
