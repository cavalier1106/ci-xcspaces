<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loginreg extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'xcspace/index';

	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('parser');
		$this->load->helper('string');
		$this->model();
		$this->library();
		$this->data['base_url'] = $this->config->item('base_url');

		//判断redis是否存在用户名键值,存在获取用户所有信息
		if( $this->is_login )
		{
			//获取用户信息
			$this->data['user_info'] = getUserInfo( $this->uname );
			//用户头像信息
			$this->data['avatar_arr'] = json_decode( unserialize( $this->data['user_info']['user_avatar'] ), true );
			//提醒系统
			$this->data['notifications'] = get_notifications( $this->uname );
		}

		$this->data['is_login'] = $this->is_login;
		if( $this->is_login )
		{
			$this->data['user_tab'] = 'user-login';
		}
		else
		{
			$this->data['user_tab'] = 'user-logout';
		}

		/* SEO处理 */
		$this->data['title'] = config_item('title');
		$this->data['keyworks'] = config_item('keyworks');
		$this->data['description'] = config_item('description');

		$this->data['host_xcspaces'] = config_item('HOST_XCSPACES');

		include_once( './weiboApi/saetv2.ex.class.php' );
		$o = new SaeTOAuthV2( config_item('WB_AKEY'), config_item('WB_SKEY') );
		$this->data['code_url'] = $o->getAuthorizeURL( config_item('WB_CALLBACK_URL') );
		
	}
	
	function model()
	{
		$this->load->model('front/lfs_loginreg','loginreg');
	}

	function library()
	{
		// $this->load->library('lib_redis');
	}
	
	/* 首页 */
	public function index()
	{
		$this->login();
	}

	/* 
	 * 登录页面 
	 */
	public function login()
	{
		$this->data['content'] = 'login';

		$post_data = $this->input->post(NULL,true);

		if( isset( $post_data['uname'] ) && isset( $post_data['passwd'] ) )
		{
			//判断用户名是否存在
			$res = $this->loginreg->check_user( $post_data );

			// echo "<pre>"; var_dump( $res ); exit;

			if( $res['code'] == 0 )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $post_data['uname'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				// setcookie('uname', $post_data['uname'], time() + $t, '/');
				// setcookie('time', time(), time() + $t, '/');

				$user_data = array(
						'uname' => $post_data['uname'],
						'user_type' => $res['user_type'],
						'time' => $time,
					);
				$_SESSION['user'] = $user_data;

			}
			$this->data['errMsg'] = $res;

			if( $res['code'] == 0 )
			{
				header( "location: " . $this->data['base_url'] );
			}
			else
			{
				$this->data['post_data'] = $post_data;
				$this->load->view($this->default_tpl, $this->data);
			}
		}
		else
		{
			$this->data['post_data'] = $post_data;
			$this->load->view($this->default_tpl, $this->data);
		}
		
	}

	/* 
	 * 退出页面 
	 */
	public function logout( $uname = 'test' )
	{
		$post_data = $this->input->post(NULL,true);

		if( isset( $post_data['uname'] ) )
		{
			$uname = strtolower( $post_data['uname'] );
		}

		if( $res = $this->redis->get( 'XCSPACES_USER[' . $uname . ']' ) )
		{
			$this->redis->del( 'XCSPACES_USER[' . $uname . ']' );
		}
		
		// setcookie('uname', '', $t, '/');
		// setcookie('time', '', time() + $t, '/');

		unset( $_SESSION );
		session_destroy();

		header("location: " . $this->data['base_url'] . "loginreg/login");
		exit(0);
	}

	/* 
	 * 注册页面 
	 */
	public function register()
	{
		$this->data['content'] = 'register';

		$post_data = $this->input->post(NULL,true);

		if( isset( $post_data['uname'] ) && isset( $post_data['passwd'] ) && isset( $post_data['email'] ) && isset( $post_data['code'] ) )
		{
			//
			$res = $this->loginreg->register_user( $post_data );

			// echo "<pre>"; var_dump( $res ); exit;

			if( $res['code'] == 0 )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $post_data['uname'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				$time = time();
				// setcookie('uname', $post_data['uname'], $time + $t, '/');
				// setcookie('time', $time, $time + $t, '/');

				$user_data = array(
						'uname' => $post_data['uname'],
						'user_type' => $res['user_type'],
						'time' => $time,
					);
				$_SESSION['user'] = $user_data;
			}

			$this->data['errMsg'] = $res;

			if( $res['code'] === 0 )
			{
				header( "location: " . $this->data['base_url'] );
			}
			else
			{
				$this->data['post_data'] = $post_data;
				$this->load->view($this->default_tpl, $this->data);
			}
		}
		else
		{
			$this->load->view($this->default_tpl, $this->data);
		}

	}
	
	/* 
	 * 忘记密码页面 
	 */
	public function forgot()
	{
		$this->data['content'] = 'forgot';
		$this->load->view($this->default_tpl, $this->data);
	}
	
	
}
