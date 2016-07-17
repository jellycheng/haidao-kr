<?php
/**
 *		绑定用户服务层
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */

class member_oauth_service extends service {
	public function __construct() {
		$this->db = $this->load->table('member/member');
		$this->oauth_db = $this->load->table('#login#member_oauth');
	}
	/* 检测第三方绑定信息 */
	public function check_user($openid = '' , $type = '') {
		if(empty($openid) || empty($type)){
			$this->error = '参数错误';
			return FALSE;
		}
		$sqlmap           = array();
		$sqlmap['openid'] = $openid;
		$sqlmap['type']   = $type;
		return $this->oauth_db->where($sqlmap)->find();
	}
	/*检查帐号是否绑定*/
	public function check_bind($member_id , $type) {
		$sqlmap           = array();
		$sqlmap['member_id'] = $member_id;
		$sqlmap['type']   = $type;
		return $this->oauth_db->where($sqlmap)->find();
	}
	/**
	 * [login_return 回调]
	 * @return [type] [description]
	 */
	public function login_return(){
		$result = $this->check_user($_GET['openid'] , $_GET['login_code']);
		if (!$result) {	// 未绑定
			$SEO = seo(0 ,'第三方登录账号绑定');
			include template('login_bind');
		} else {
			$userInfo = $this->db->find($result['member_id']);
			if (!$userInfo) showmessage('该用户不存在');
			switch ($userInfo['status']) {
				case '-1':
					showmessage('该账号已被删除！');
					break;
				case '0':
					showmessage('该账号未认证！');
					break;
				case '2':
					showmessage('该账号已被禁用！');
					break;
				default:
					//登录
					
					break;
			}
		}
	}
	/**
	 * [login_bind 绑定已有帐号]
	 * @return [type] [description]
	 */
	public function login_bind(){
		extract($_GET);
		if (empty($type)){
			$this->error = '第三方登录类型有误！';
			return FALSE;
		}  
		if (empty($openid)){
			$this->error = '第三方登录会员信息有误！';
			return FALSE;
		} 
		$sqlmap = array();
		$sqlmap['username'] = $username;
		$userInfo = $this->db->where($sqlmap)->find();
		if (!$userInfo || md5(md5($password).$userInfo['encrypt']) != $userInfo['password']) {
			showmessage('用户名或密码错误');
		} else {
			switch ($userinfo['status']) {
				case '-1':
					showmessage('该账号已被删除！');
					break;
				case '0':
					showmessage('该账号未认证！');
					break;
				case '2':
					showmessage('该账号已被禁用！');
					break;
				default:
					// 绑定第三方账号
					$result = $this->check_bind($userInfo['id'] , $type);
					if ($result) {
						showmessage('该第三方登录账号已绑定！');
					}
					$data = array();
					$data['member_id']  = $userInfo['id'];
					$data['openid']   = $openid;
					$data['type']     = $type;
					$data['dateline'] = NOW_TIME;
					$_result = $this->oauth_db->add($data);
					
					
					break;
			}
		}
	}
	
	/**
	 * [reg_bind 绑定注册]
	 * @return [type] [description]
	 */
	/*public function reg_bind() {
		if(C('reg_isreg') == 0) showmessage('站点已关闭注册，绑定失败');
		if (empty($_GET['type']))   showmessage('第三方登录类型有误！');
		if (empty($_GET['openid'])) showmessage('第三方登录会员信息有误！');
		$username = trim($_GET['username']);
		$password = trim($_GET['password']);
		$confirm_pwd = trim($_GET['confirm_pwd']);
		if ($username == '' || is_numeric($username) || is_email($username)) {
			showmessage('用户名不能为纯数字或邮箱');
		}
		if ($password == '' || $password !== $confirm_pwd) {
			showmessage('两次密码不一致');
		}
		$data = array();
		$data['username'] = $username;
		$data['valid'] = random(10);
		$data['password'] = $password ? md5($data['valid'].$password) : false;
		$result_uid = $this->db->update($data);
		if($result_uid){
			// 绑定第三方账号
			$_result = $this->check_bind($result_uid , $_GET['type']);
			if ($_result) {
				showmessage('该第三方登录账号已绑定！');
			}
			$data = array();
			$data['user_id']  = $result_uid;
			$data['openid']   = $_GET['openid'];
			$data['type']     = $_GET['type'];
			$data['dateline'] = NOW_TIME;
			$result_add = $this->oauth_db->add($data);
			if (!$result_add) showmessage('第三方登录绑定失败，请从新绑定');
			$this->putlogin($result_uid , $username);
			runhook('user_register_success');
	        runhook('n_reg_success',array('user_id'=>$result_uid));
	        runhook('user_login_success');
			showmessage('第三方登录绑定成功', $url_forward, 1);
		}else{
			showmessage($this->db->getError());
		}*/
		
	
}