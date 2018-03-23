<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WbLogin extends CI_Controller {
	
	public $data = array();
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
		$this->data['is_login'] = false;

		/* SEO处理 */
		$this->data['title'] = config_item('title');
		$this->data['keyworks'] = config_item('keyworks');
		$this->data['description'] = config_item('description');
		
	}
	
	function model()
	{
		$this->load->model('front/lfs_wb_user','wb_user');
		$this->load->model('front/lfs_user','user');
	}

	function library()
	{
		// $this->load->library('lib_redis');
	}
	
	/* 首页 */
	public function index()
	{
		// $this->login();
	}

	/* 
	 * 登录页面 
	 */
	public function login()
	{
		$this->data['content'] = 'wbLogin';

		include_once( './weiboApi/saetv2.ex.class.php' );
		$WB_AKEY = config_item('WB_AKEY');
		$WB_SKEY = config_item('WB_SKEY');

		$o = new SaeTOAuthV2( $WB_AKEY, $WB_SKEY );
		
		$token = '';

		if ( isset($_REQUEST['code']) ) 
		{
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = config_item('WB_CALLBACK_URL');
			try 
			{
				$token = $o->getAccessToken( 'code', $keys ) ;
			} 
			catch (OAuthException $e) 
			{
				//
			}
		}

		if ( $token ) 
		{
			//授权完成
			$_SESSION['token'] = $token;
			setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
		}
		else
		{
			// echo "授权失败";
			// exit();
			header( "location: " . $this->data['base_url'] );
		}

		$wb_info = array();

		if( isset($_SESSION['token']) )
		{
			$c = new SaeTClientV2( $WB_AKEY , $WB_SKEY , $_SESSION['token']['access_token'] );
			$ms  = $c->home_timeline(); // done
			$uid_get = $c->get_uid();
			$uid = $uid_get['uid'];
			$wb_info = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
			$_SESSION['wb_info'] = $wb_info;
		}

		// echo "<pre>";var_dump($ms);
		// echo "<pre>";var_dump($wb_info);exit;

		if( $wb_info )
		{
			// 1 用$wb_info['id']在表xcspace_users的wbBind字段查数据是否有账号绑定
			// 2 如果有绑定直接获取信息写入session,写入redis登陆;如果没有绑定,则跳转到wbLogin绑定页面填写信息登陆
			// 3 
			
			// if( !isset($wb_info['id']) ) echo "登陆参数异常";exit();

			$res = $this->user->WeiboIsBindUser( $wb_info['id'] );

			if( $res )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $res['name'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				$user_data = array(
						'uname' => $res['name'],
						'time' => $time,
						'isWeiBoLogin' => true,
						'isQQLogin' => false,
						'wb_info' => $wb_info,
					);
				$_SESSION['user'] = $user_data;
				unset( $user_data );

				header( "location: " . $this->data['base_url'] );
			}
			else
			{
				$this->load->view($this->default_tpl, $this->data);
			}
		}
		else
		{
			$this->load->view($this->default_tpl, $this->data);
		}
	}

	/*
	 * 绑定已有账号
	 */
	public function bindHaveUser()
	{
		$this->data['content'] = 'wbLogin';

		$email = $this->input->post('email',true);
		$res = $this->wb_user->is_binded_user( $email, $_SESSION['wb_info'] );

		// echo "<pre>"; var_dump( $res ); exit;

		if( isset( $res['name'] ) )
		{
			$this->redis->set('XCSPACES_USER[' . strtolower( $res['name'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
			$user_data = array(
					'uname' => $res['name'],
					'time' => time(),
					'isWeiBoLogin' => true,
					'isQQLogin' => false,
					'wb_info' => $_SESSION['wb_info'],
				);
			$_SESSION['user'] = $user_data;

			unset($_SESSION['wb_info']);
			unset( $user_data );

			header( "location: " . $this->data['base_url'] );
		}
		else
		{
			if( isset($res['code']) ) $this->data['errMsg'] = $res;
			$this->load->view($this->default_tpl, $this->data);
		}
	}

	/*
	 * 绑定新账号
	 */
	public function bindNoUser()
	{
		$this->data['content'] = 'wbLogin';
		
		$post_data = $this->input->post(NULL,true);

		if( isset( $post_data['uname'] ) && isset( $post_data['passwd'] ) && isset( $post_data['email'] ) )
		{
			$res = $this->wb_user->wb_register_user( $post_data, $_SESSION['wb_info'] );
			// echo "<pre>";var_dump($res);exit;
			if( $res['code'] == 0 )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $post_data['uname'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				$time = time();
				$user_data = array(
						'uname' => $post_data['uname'],
						'time' => $time,
						'isWeiBoLogin' => true,
						'isQQLogin' => false,
						'wb_info' => $_SESSION['wb_info'],
					);
				$_SESSION['user'] = $user_data;
				unset( $_SESSION['wb_info'] );
				unset( $user_data );
			}

			if( isset($res['code']) && $res['code'] != 0 ) $this->data['errMsg'] = $res;

			if( $res['code'] == 0 )
			{
				header( "location: " . $this->data['base_url'] );
			}
			else
			{
				$this->load->view($this->default_tpl, $this->data);
			}
		}
		else
		{
			$this->load->view($this->default_tpl, $this->data);
		}
	}
	
}
