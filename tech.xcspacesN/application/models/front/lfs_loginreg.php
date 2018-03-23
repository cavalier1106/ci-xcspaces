<?php
class lfs_loginreg extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 
     * 用户登录处理 
     */
	function check_user( $post_data = array() )
	{
		//addslashes() 如果CI框架本身有处理过的话,就不用在作处理 --- 安全;
		
		$users = $this->db->dbprefix("users");
		$uname = $post_data['uname'];
		$sql = "SELECT id,sign,user_type FROM `{$users}` WHERE name='$uname' LIMIT 1";
		$res = $this->db->query($sql)->row_array();

		$msg = array();

		if( $res )
		{
			$pwd = md5( $post_data['passwd'] . $res['sign'] );
			$sql = "SELECT id,status FROM `{$users}` WHERE name='{$uname}' AND passwd='{$pwd}' LIMIT 1";

			$ret = $this->db->query($sql)->row_array();

			if( $ret )
			{
				if($ret['status']==0){
					$msg['code'] = 0;
					$msg['uname'] = $uname;
					$msg['user_type'] = $res['user_type'];
					$msg['msg'] = '用户登录成功';
				} 
				if($ret['status']==1){
					$msg['code'] = 2;
					$msg['uname'] = $uname;
					$msg['msg'] = '该用户已封号';
				}
			}
			else
			{
				$msg['code'] = 1;
				$msg['msg'] = '用户名和密码不匹配';
			}
		}
		else
		{
			if( empty( $uname ) )
			{
				$msg['code'] = 3;
				$msg['msg'] = '用户名不能为空';
			}
			else
			{
				$msg['code'] = 4;
				$msg['msg'] = '用户名不存在';
			}
		}

		return $msg;
	}

	/* 
     * 用户注册处理 
     */
	function register_user( $post_data = array() )
	{
		//addslashes() 如果CI框架本身有处理过的话,就不用在作处理 --- 安全;
		//----------------------------------------
		// code验证码处理
		//----------------------------------------
		
		$users = $this->db->dbprefix("users");

		$uname = $post_data['uname'];
		$passwd = $post_data['passwd'];
		$email = $post_data['email'];
		$code = $post_data['code'];

		if($this->input->get('show')==1){
			var_dump($post_data);exit;
		}

		/*
		 * 用户名格式验证处理 ---- 英文、数字、下划线6-20位字符
		 */
		$strlen = strlen( $uname ); 

		if( $strlen < 6 )
		{
			$msg['code'] = 6;
			$msg['msg'] = '用户名不能少于6位';

			return $msg;
		}

		if( $strlen > 20 )
		{
			$msg['code'] = 7;
			$msg['msg'] = '用户名不能超过20位';

			return $msg;
		}

		$preg='/^[\w\_]{6,20}$/u';
		if( !preg_match( $preg, $uname ) )
		{
			$msg['code'] = 8;
			$msg['msg'] = '用户名格式：英文、数字、下划线6-20位字符组成';

			return $msg;
		}

		/*
		 * 用户名密码验证
		 */

		if( empty( $passwd ) )
		{
			$msg['code'] = 9;
			$msg['msg'] = '密码不能为空';

			return $msg;
		}

		/*
		 * 用户名注册验证码
		 */
		$user_type = 1;
		$autoCreatAccount = 0;
		if(isset($post_data['autoCreatAccount']))
		{
			$autoCreatAccount = 1;
			$user_type = 2;
		}
		// 过滤自动生成账号操作
		if($autoCreatAccount == 0)
		{
			if( !(int)( strtolower( $code ) === strtolower( $_SESSION['captcha'] ) ) )
			{
				$msg['code'] = 10;
				$msg['msg'] = '验证码不匹配';

				return $msg;
			}
		}

		$sql = "SELECT id FROM `{$users}` WHERE name='$uname' LIMIT 1";
		$res = $this->db->query($sql)->row_array();

		$msg = array();
		// 判断用户名是否存在
		if( empty( $res ) )
		{
			$sign = random_string();
			$pwd = md5( $passwd . $sign );

			// 邮箱格式验证处理
			$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
			if ( !preg_match( $pattern, $email ) )
			{
				$msg['code'] = 5;
				$msg['msg'] = '输入的电子邮件地址格式不正确';

				return $msg;
			}

			$sql = "SELECT id FROM `{$users}` WHERE email='{$email}' LIMIT 1";
			$ret = $this->db->query($sql)->row_array();
			// 判断用户email是否存在
			if( empty( $ret ) )
			{
				//------------------------------------------
				// +++++++++++++++ 事务处理
				//------------------------------------------

				$num = rand(1,5);
				$default_img = config_item('default_img');

				// 数据插入数据库
				$time = time();
				$ip = real_ip();
				$data = array(
						'name' => $uname,
						'nickname' => '',
						'passwd' => $pwd,
						'email' => $email,
						'phone' => '',
						'img_path' => $default_img[$num],
						'qq' => '',
						'sex' => '',
						'city' => '',
						'address' => '',
						'status' => 0,
						'is_online' => '',
						'user_type' => $user_type,
						'website' => '~ 主人还没有填写博客地址 ~',
						'person_bio' => '~ 主人有点懒还没有写简介喔 ~',
						'reg_time' => $time,
						'login_time' => $time,
						'logout_time' => $time,
						'reg_ip' => $ip,
						'login_ip' => $ip,
						'sign' => $sign,
					);

				//事务开启
				$this->db->trans_start();

				$res = $this->db->insert('users', $data);
				//获取插入用户ID
				$insert_id = $this->db->insert_id();

				$users_expand = array(
						'uid' => $insert_id,
						'gold' => 0,
						'silver' => 0,
						'coin' => 200, //初始威望值
					);
				$ret = $this->db->insert('users_expand', $users_expand);

				//hash分表处理
				$uid = (int)$insert_id;
				$uid_hash = get_hash( $uid );
				$table = 'coin_record_' . $uid_hash;
				create_coin_record_table( $uid_hash );

				// 威望值记录
				$record_data = array(
						'uid' => $insert_id,
						'coin' => 200,
						'desc' => '初始威望值',
						'op_type' => 0,
						'random' => random_string(),
						'time' => $time,
					);
				$recrd = $this->db->insert($table, $record_data);

				// 登陆系统提醒记录
				$notifications_data = array(
						'rId' => $insert_id,
						'title' => '欢迎注册xcspaces账号',
						'content' => '欢迎来到 xcspaces ,注册成功获得初始威望值 +200',
						'type' => 2,
						'view_status' => 0,
						'fromUserId' => '1',
						'fromUserName' => '系统管理员',
						'time' => $time,
					);
				$nres = $this->db->insert('notifications', $notifications_data);

				//事务完成
				$this->db->trans_complete();

				if( $res && $ret && $recrd && $nres)
				{
					$msg['code'] = 0;
					$msg['user_type'] = $user_type;
					$msg['msg'] = '用户注册成功';
				}
				else
				{
					$msg['code'] = 1;
					$msg['msg'] = '数据保存异常';
				}
			}
			else
			{
				$msg['code'] = 3;
				$msg['msg'] = '用户邮箱已经存在';
			}
		}
		else
		{
			$msg['code'] = 4;
			$msg['msg'] = "用户名 {$uname} 已经被注册，请重新选择一个";
		}

		return $msg;
	}
	
}

?>
