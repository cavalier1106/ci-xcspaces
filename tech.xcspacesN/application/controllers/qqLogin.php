<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class qqLogin extends CI_Controller {
	
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
		$this->load->model('front/lfs_qq_user','qq_user');
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
		require_once("./qqApi/qqConnectAPI.php");
		$qc = new QC();
		$qc->qq_login();
	}

	/* 
	 * 登录回调 
	 */
	public function loginCallback()
	{
		$this->data['content'] = 'qqLogin';

		require_once("./qqApi/qqConnectAPI.php");
		$qc = new QC();
		$access_token = $qc->qq_callback();
		$qq_openid = $qc->get_openid();
		
		$qc = new QC($access_token, $qq_openid);
		$qq_info = $qc->get_user_info();
		$qq_info['openid'] = $qq_openid;

		if( isset( $qq_info['ret'] ) && $qq_info['ret'] == 0 )
		{
			$_SESSION['qq_info']['openid'] = $qq_info['openid'];
			// $_SESSION['qq_info']['access_token'] = $access_token;
			$_SESSION['qq_info']['gender'] = $qq_info['gender'];
			$_SESSION['qq_info']['nickname'] = $qq_info['nickname'];
			$_SESSION['qq_info']['figureurl'] = $qq_info['figureurl'];
			$_SESSION['qq_info']['figureurl_1'] = $qq_info['figureurl_1'];
			$_SESSION['qq_info']['figureurl_2'] = $qq_info['figureurl_2'];
			$_SESSION['qq_info']['vip'] = $qq_info['vip'];
			$_SESSION['qq_info']['level'] = $qq_info['level'];
			$_SESSION['qq_info']['is_yellow_year_vip'] = $qq_info['is_yellow_year_vip'];

			// 1 用$qq_info['id']在表xcspace_users的wbBind字段查数据是否有账号绑定
			// 2 如果有绑定直接获取信息写入session,写入redis登陆;如果没有绑定,则跳转到qqLogin绑定页面填写信息登陆
			// 3 
			
			// if( !isset($qq_info) ) echo "登陆参数异常";exit();

			$res = $this->user->qqBindUser( $qq_openid );

			if( $res )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $res['name'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				$user_data = array(
						'uname' => $res['name'],
						'time' => time(),
						'isWeiBoLogin' => false,
						'isQQLogin' => true,
						'qq_info' => $qq_info,
					);
				$_SESSION['user'] = $user_data;

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
		$this->data['content'] = 'qqLogin';

		$email = $this->input->post('email',true);
		$res = $this->qq_user->is_binded_user( $email, $_SESSION['qq_info'] );

		// echo "<pre>"; var_dump( $res ); exit;

		if( isset( $res['name'] ) )
		{
			$this->redis->set('XCSPACES_USER[' . strtolower( $res['name'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
			$user_data = array(
					'uname' => $res['name'],
					'time' => time(),
					'isWeiBoLogin' => flase,
					'isQQLogin' => ture,
					'qq_info' => $_SESSION['qq_info'],
				);
			$_SESSION['user'] = $user_data;

			unset($_SESSION['qq_info']);

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
		$this->data['content'] = 'qqLogin';
		
		$post_data = $this->input->post(NULL,true);

		if( isset( $post_data['uname'] ) && isset( $post_data['passwd'] ) && isset( $post_data['email'] ) )
		{
			$res = $this->qq_user->wb_register_user( $post_data, $_SESSION['qq_info'] );
			// echo "<pre>";var_dump($res);exit;
			if( $res['code'] == 0 )
			{
				$this->redis->set('XCSPACES_USER[' . strtolower( $post_data['uname'] ) . ']', serialize( $res ), config_item('USER_LOGIN_SAVE_TIME'));
				$time = time();
				$user_data = array(
						'uname' => $post_data['uname'],
						'time' => $time,
						'isWeiBoLogin' => false,
						'isQQLogin' => true,
						'qq_info' => $_SESSION['qq_info'],
					);
				$_SESSION['user'] = $user_data;
				unset( $_SESSION['qq_info'] );
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
