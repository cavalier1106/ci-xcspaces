<?php
class Lfs_qq_user extends CI_Model {
	
	private $_tb;
	private $_pre_tb;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_tb = 'qq_users';
        $this->_pre_tb = $this->db->dbprefix("qq_users");
    }

	/* 
     * 绑定已有用户账户 
     */
	function is_binded_user( $email = '', $qq_info = array() )
	{
		if( !$qq_info ) 
		{
			$msg['code'] = '7001';
			$msg['msg'] = 'QQ参数错误';

			return $msg;
		}

		//addslashes() 如果CI框架本身有处理过的话,就不用在作处理 --- 安全;
		$users = $this->db->dbprefix("users");

		$msg = array();

		$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
		if ( !preg_match( $pattern, $email ) )
		{
			$msg['code'] = 1;
			$msg['msg'] = '输入的电子邮件地址格式不正确';

			return $msg;
		}
		//该邮箱是否被绑定？
		$sql = "SELECT name FROM `{$users}` WHERE email='{$email}' AND qqBind=1 LIMIT 1";
		$res = $this->db->query($sql)->row_array();
		if($res)
		{
			$msg['code'] = 2;
			$msg['msg'] = '邮箱已经被绑定';

			return $msg;
		}

		$sql = "SELECT name FROM `{$users}` WHERE email='{$email}' LIMIT 1";
		$res = $this->db->query($sql)->row_array();

		// 判断用户名是否存在
		if( empty( $res ) )
		{
			$msg['code'] = 3;
			$msg['msg'] = '用户邮箱不存在';
			return $msg;
		}

		//修改用户表信息绑定新浪QQ
		$data = array(
				'qqBind' => 1,
				'qqOpenid' => $qq_info['openid'],
				'qqBindTime' => time(),
			);
		$this->db->where('email', $email);
		$this->db->update('users', $data);

		//记录一条QQ信息
		$insert_res = $this->record_qq_info( $qq_info );

		return $res;
	}

	/* 
     * weibo用户注册 
     */
	function wb_register_user( $post_data = array(), $qq_info = array() )
	{
		if( !$qq_info ) 
		{
			$msg['code'] = '7001';
			$msg['msg'] = 'QQ参数错误';

			return $msg;
		}
		
		//addslashes() 如果CI框架本身有处理过的话,就不用在作处理 --- 安全;
		//----------------------------------------
		// code验证码处理
		//----------------------------------------
		
		$users = $this->db->dbprefix("users");

		$uname = $post_data['uname'];
		$passwd = $post_data['passwd'];
		$email = $post_data['email'];

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
						'qqBind' => 1,
						'qqOpenid' => $qq_info['openid'],
						'qqBindTime' => $time,
						'is_online' => '',
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
				$this->db->trans_begin();

				$res = $this->db->insert('users', $data);
				//获取插入用户ID
				$insert_id = $this->db->insert_id();
				
				$users_expand = array(
						'uid' => $insert_id,
						'gold' => 0,
						'silver' => 0,
						'coin' => 200,//初始威望值
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

				//记录一条QQ信息
				$insert_res = $this->record_qq_info( $qq_info );

				//事务完成
				if ($this->db->trans_status() === FALSE)
				{
				    $this->db->trans_rollback();
				    $msg['code'] = 1;
					$msg['msg'] = '数据保存异常';
				}
				else
				{
				    $this->db->trans_commit();
				    $msg['code'] = 0;
					$msg['msg'] = '用户注册成功';
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

	/*
	 * 记录一条QQ信息
	 */
	function record_qq_info( $qq_info = array() )
	{
		//先检查是否有 QQ 记录

		$sql = "SELECT qq_openid FROM `{$this->_pre_tb}` WHERE qq_openid=\"{$qq_info['openid']}\" LIMIT 1";
		$res = $this->db->query($sql)->row_array();

		if( !$res )
		{
			//记录一条QQ信息
			$real_ip = real_ip();
			$time = time();

			$data = array(
					'qq_NickName' => $qq_info['nickname'],
					'qq_gender' => $qq_info['gender'],
					'qq_openid' => $qq_info['openid'],
					'qq_figureurl' => $qq_info['figureurl'],
					'qq_figureurl_1' => $qq_info['figureurl_1'],
					'qq_figureurl_2' => $qq_info['figureurl_2'],
					'qq_vip' => $qq_info['vip'],
					'qq_level' => $qq_info['level'],
					'qq_is_yellow_year_vip' => $qq_info['is_yellow_year_vip'],
					'qq_reg_time' => $time,
					'qq_login_time' => $time,
					'qq_logout_time' => $time,
					'qq_reg_ip' => $real_ip,
					'qq_login_ip' => $real_ip,
				);

			return $this->db->insert($this->_tb, $data);
		}
		else
		{
			return true;
		}
		
	}

}

?>
