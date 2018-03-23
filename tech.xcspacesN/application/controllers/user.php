<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'xcspace/index';

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->library('parser');
		$this->model();
		$this->library();
		$this->data['base_url'] = $this->config->item('base_url');

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

		/* 
		 * http://xcspace.com/?tab=tech
		 * 空间功能处理 
		 */
		if(isset($_GET['tab']))
		{
			$tab = $_GET['tab'];
		}
		else
		{
			$tab = 'home';
		}

		$nodes = $this->nodes->get_nodes();
		$this->data['nodes'] = $nodes;
		
		/*
		 * 所有空间 特殊处理
		 */
		if( $tab != 'home' )
		{
			$this->data['znodes'] = $nodes[$tab];
		}
		else
		{
			$znodes = $this->nodes->get_znodes();
			$this->data['znodes'] = array('zid' => $znodes);
		}

		$this->data['tab'] = $tab;

		/* SEO处理 */
		$this->data['title'] = config_item('title');
		$this->data['keyworks'] = config_item('keyworks');
		$this->data['description'] = config_item('description');

		$this->data['host_xcspaces'] = config_item('HOST_XCSPACES');
		
	}
	
	function model()
	{
		$this->load->model('front/lfs_nodes','nodes');
		$this->load->model('front/lfs_user','user');
		$this->load->model('front/lfs_ads','ads');
		$this->load->model('front/lfs_notepad','notepad');
	}

	function library()
	{
		// $this->load->library('gettreeid');
	}
	
	/* 首页 */
	public function index()
	{
		$this->userCenter();
	}

	/* 
	 * 用户中心页面 
	 */
	public function userCenter()
	{
		$this->data['content'] = 'userCenter';

		//--------------------

		$this->load->view($this->default_tpl, $this->data);
	}
	
	/* 
	 * 用户设置页面 
	 */
	public function setUserInfo()
	{
		$this->data['content'] = 'setUserInfo';

		if(isset($_GET['utab']))
		{
			$tab = $_GET['utab'];
		}
		else
		{
			$tab = 'persion';
		}

		$this->data['utab'] = $tab;

		if( $this->uname )
		{
			if( $post_data = $this->input->post(NULL,true) )
			{
				//判断redis是否存在用户名键值,存在获取用户所有信息
				if( unserialize( $this->redis->get("XCSPACES_USER[{$this->uname}]") ) )
				{
					//保存用户信息
					$this->user->set_userInfo( $this->uname, $post_data );
				}
			}
			
			$this->data['user_setting'] = $this->user->get_userInfo( $this->uname );
			/* 
			 * 获取用户的笔记 
			 */
			$this->data['notepad'] = $this->notepad->get_notepad( $this->data['user_info'] );

			/* 
			 * 可以根据用户喜好设置关联广告 
			 */
			$znode_name = "tech";
			$ads_list = $this->ads->get_ads( $znode_name );
			$this->data['ads_list'] = $ads_list;

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/* 
	 * 用户中心页面 
	 */
	public function member( $uid = 0 )
	{
		$this->data['content'] = 'member';

		/* 
		 * 置顶广告显示处理 
		 */
		$znode_name = 'tech';
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;
		//获取用户信息
		$this->data['user_center'] = $this->user->get_user_center_info( (int)$uid );
		$this->data['one_user_info'] = getUserInfo2( (int)$uid );

		// 登录数据	
		if( $this->is_login )
		{
			//是否收藏 --------------------
			$users_arr = explode( ',', $this->data['user_info']['users_str'] );
			if( in_array($uid, $users_arr) )
			{
				$this->data['is_favorite'] = true;
			}
			else
			{
				$this->data['is_favorite'] = false;
			}
		}

		//--------------------
		
		$this->load->view($this->default_tpl, $this->data);
		
	}

	/* 
	 * 用户话题页面分页
	 */
	public function memberTopics( $uid = 0, $page = 1 )
	{
		$this->data['content'] = 'memberTopics';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		//判断是否有cookie值
		// if( $this->uname )
		// {
			//判断redis是否存在用户名键值,存在获取用户所有信息
			if( unserialize( $this->redis->get("XCSPACES_USER[{$this->uname}]") ) )
			{
				//是否收藏 --------------------
				$users_arr = explode( ',', $this->data['user_info']['users_str'] );
				if( in_array($uid, $users_arr) )
				{
					$this->data['is_favorite'] = true;
				}
				else
				{
					$this->data['is_favorite'] = false;
				}
			}

			//获取用户信息
			$this->data['one_user_info'] = getUserInfo2( $uid );
			/* 
			 * 置顶广告显示处理 
			 */
			$znode_name = 'tech';
			$ads = $this->ads->get_top_ads( $znode_name );
			$this->data['ads'] = $ads;

			/* 
			 *  我关注用户主题总数
			 */
			$member_topics_count = $this->user->member_topics_count( $uid );
			$this->data['member_info_count'] = $member_topics_count;

			/* 
			 * 我的收藏主题列表
			 */
			$setting =array(
				'base_url' => 'user/memberTopics/' . $uid,
				'per_page' => 15,
				'uri_segment' => 4
			);
			$member_topics = $this->user->memberTopics_findList( $page, $setting['per_page'], $uid);
			$setting['total_rows'] = $member_topics_count;
			//我的收藏主题总数
			$this->data['count'] = $member_topics_count;
			$this->data['page'] = pagination($setting);
			$this->data['datas'] = $member_topics['data'];
			//当前页数
			$this->data['current_page'] = $page;
			//总页数
			if( $member_topics_count % $setting['per_page'] == 0 )
			{
				$this->data['page_total'] = $member_topics_count / $setting['per_page'];
			}
			else
			{
				$this->data['page_total'] = (int)($member_topics_count / $setting['per_page']) + 1;
			}

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		// }
		// else
		// {
		// 	header( "location: " . $this->data['base_url'] );
		// }
	}

	/* 
	 * 用户笔记页面分页
	 */
	public function memberNotepad( $uid = 0, $page = 1 )
	{
		$this->data['content'] = 'memberNotepad';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		//判断是否有登陆
		if( $this->is_login )
		{
			//是否收藏 --------------------
			$users_arr = explode( ',', $this->data['user_info']['users_str'] );
			if( in_array($uid, $users_arr) )
			{
				$this->data['is_favorite'] = true;
			}
			else
			{
				$this->data['is_favorite'] = false;
			}

			//获取用户信息
			$this->data['one_user_info'] = getUserInfo2( $uid );

			/* 
			 *  我关注用户主题总数
			 */
			$member_notepad_count = $this->notepad->member_notepad_count( $uid );
			$this->data['member_notepad_count'] = $member_notepad_count;

			/* 
			 * 我的收藏主题列表
			 */
			$setting =array(
				'base_url' => 'user/memberNotepad/' . $uid,
				'per_page' => 15,
				'uri_segment' => 4
			);
			$memberNotepad = $this->notepad->memberNotepad_findList( $page, $setting['per_page'], $uid);
			$setting['total_rows'] = $member_notepad_count;
			//我的收藏主题总数
			$this->data['count'] = $member_notepad_count;
			$this->data['page'] = pagination($setting);
			$this->data['datas'] = $memberNotepad['data'];
			//当前页数
			$this->data['current_page'] = $page;
			//总页数
			if( $member_notepad_count % $setting['per_page'] == 0 )
			{
				$this->data['page_total'] = $member_notepad_count / $setting['per_page'];
			}
			else
			{
				$this->data['page_total'] = (int)($member_notepad_count / $setting['per_page']) + 1;
			}

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/**
	 * 设置笔记本 开放
	 */
	function notepad_ajax()
	{
		if( $this->is_login )
		{
			// $this ->security->csrf_verify(); //csrf检查
			$post_data = $this->input->post(NULL,true);

			if( !empty( $post_data ) )
			{
				//保存回复数据
				$msg = $this->notepad->set_notepad_isopen( $post_data, $this->data['user_info'] );
				echo json_encode( $msg );
				exit();
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/* 
	 * 用户话题回复页面分页 
	 */
	public function memberReplys( $uid = 0, $page = 1 )
	{
		$this->data['content'] = 'memberReplys';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		//判断是否有cookie值
		if( $this->is_login )
		{
			//判断redis是否存在用户名键值,存在获取用户所有信息
			if( unserialize( $this->redis->get("XCSPACES_USER[{$this->uname}]") ) )
			{
				//是否收藏 --------------------
				$users_arr = explode( ',', $this->data['user_info']['users_str'] );
				if( in_array($uid, $users_arr) )
				{
					$this->data['is_favorite'] = true;
				}
				else
				{
					$this->data['is_favorite'] = false;
				}
			}
		}

		//获取用户信息
		$this->data['one_user_info'] = getUserInfo2( $uid );
		
		/* 
		 * 置顶广告显示处理 
		 */
		$znode_name = 'tech';
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;

		/* 
		 *  我关注用户主题总数
		 */
		$member_replys_count = $this->user->member_replys_count( $uid );
		$this->data['member_info_count'] = $member_replys_count;

		/* 
		 * 我的收藏主题列表
		 */
		$setting =array(
			'base_url' => 'user/memberReplys/' . $uid,
			'per_page' => 15,
			'uri_segment' => 4
		);
		$replys_topics = $this->user->memberReplys_findList( $page, $setting['per_page'], $uid);
		$setting['total_rows'] = $member_replys_count;
		//我的收藏主题总数
		$this->data['count'] = $member_replys_count;
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $replys_topics['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $member_replys_count % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $member_replys_count / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($member_replys_count / $setting['per_page']) + 1;
		}

		//--------------------
		
		$this->load->view($this->default_tpl, $this->data);
	}
	
	/*
	 * 0条未读提醒
	 */
	function notifications( $page = 1 )
	{
		$this->data['content'] = 'notifications';

		/* 
		 * http://xcspace.com/?tab=tech
		 * 空间功能处理 
		 */
		if(isset($_GET['uid']))
		{
			$uid = (int)$_GET['uid'];
		}
		else
		{
			$uid = 0;
		}

		//判断是否有cookie值
		if( $this->uname )
		{
			//判断redis是否存在用户名键值,存在获取用户所有信息
			if( unserialize( $this->redis->get("XCSPACES_USER[{$this->uname}]") ) )
			{
				//是否收藏 --------------------
				$users_arr = explode( ',', $this->data['user_info']['users_str'] );
				if( in_array($uid, $users_arr) )
				{
					$this->data['is_favorite'] = true;
				}
				else
				{
					$this->data['is_favorite'] = false;
				}
				
				//获取用户信息
				$this->data['one_user_info'] = getUserInfo2( $uid );
				
				// 设置提醒为已查看
				$this->load->model('front/Lfs_notifications','notifications');
				$this->notifications->setNotifications( $uid );

				/* 
				 * 置顶广告显示处理 
				 */
				// $znode_name = 'tech';
				// $ads = $this->ads->get_top_ads( $znode_name );
				// $this->data['ads'] = $ads;

				/* 
				 *  未读提醒总数
				 */
				// $notifications_replys_count = get_notifications( $this->data['user_info']['name'] );
				$notifications_replys_count = get_notifications( $this->uname, 2 );
				$this->data['notifications_replys_count'] = $notifications_replys_count;

				/* 
				 * 未读提醒列表
				 */
				$setting =array(
					'base_url' => 'user/notifications',
					'per_page' => 15,
					'uri_segment' => 3
				);
				$notifications_replys = get_notifications( $this->uname, 3, $page, $setting['per_page'] );

				$setting['total_rows'] = $notifications_replys_count;
				//我的未读提醒总数
				$this->data['count'] = $notifications_replys_count;
				$this->data['page'] = pagination($setting);
				$this->data['datas'] = $notifications_replys;
				//当前页数
				$this->data['current_page'] = $page;
				//总页数
				if( $notifications_replys_count % $setting['per_page'] == 0 )
				{
					$this->data['page_total'] = $notifications_replys_count / $setting['per_page'];
				}
				else
				{
					$this->data['page_total'] = (int)($notifications_replys_count / $setting['per_page']) + 1;
				}
			}

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/* 头像上传 */
	function avatar()
	{
		$this->data['content'] = 'avatar';

		if(isset($_GET['utab']))
		{
			$tab = $_GET['utab'];
		}
		else
		{
			$tab = 'persion';
		}

		$this->data['utab'] = $tab;

		if( $this->is_login )
		{
			$post_data = $this->input->post(NULL,true);
			$uid = (int)$post_data['uid'];

			// echo "<pre>";var_dump( $_FILES );exit;

			if (!empty($_FILES) && $_FILES['avatar']['error'] === 0) 
			{
				$this->load->library('lib_image');

				$tempFile = $_FILES['avatar']['tmp_name'];
				$orgFile = $_FILES['avatar']['name'];
				
				$url = dirname( dirname( dirname( __FILE__ ) ) );

				//没有权限是不保存图片的
				make_dir( $url . '/images' );
				make_dir( $url . '/images/avatar' );
				make_dir( $url . '/images/avatar/' . $uid );
				$large_imgUrl = $url . '/images/avatar/' . $uid . '/' . $uid . '_large.png';
				$normal_imgUrl = $url . '/images/avatar/' . $uid . '/' . $uid . '_normal.png';
				$mini_imgUrl = $url . '/images/avatar/' . $uid . '/' . $uid . '_mini.png';
				
				/* 把圖片格式統一為 png */
				$imgArr = getimagesize($tempFile);
				$imgWidth = $imgArr[0];
				$imgHeight = $imgArr[1];

				$this->lib_image->ImageToJPG($tempFile,$large_imgUrl, $imgWidth, $imgHeight);
				$this->lib_image->ImageToJPG($large_imgUrl,$normal_imgUrl, $imgWidth, $imgHeight);
				$this->lib_image->ImageToJPG($large_imgUrl,$mini_imgUrl, $imgWidth, $imgHeight);

				/*$this->lib_image->ImageToJPG($tempFile,$large_imgUrl, 73, 73);
				$this->lib_image->ImageToJPG($large_imgUrl,$normal_imgUrl, 48, 48);
				$this->lib_image->ImageToJPG($large_imgUrl,$mini_imgUrl, 24, 24);*/

				//保存头像数据到users表
				$avatar_arr = array(
						'large' => $uid . '_large.png',
						'normal' => $uid . '_normal.png',
						'mini' => $uid . '_mini.png',
					);
				$avatar_str = serialize( json_encode( $avatar_arr ) );
				$this->user->set_userAvatar( $this->uname, $uid, $avatar_str );

				// echo "<pre>";var_dump( $_FILES);exit;
			}

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/**
	 * 修改用户密码 
	 */
	function setpasswd()
	{
		$this->data['content'] = 'setPasswd';

		if(isset($_GET['utab']))
		{
			$tab = $_GET['utab'];
		}
		else
		{
			$tab = 'persion';
		}

		$this->data['utab'] = $tab;

		if( $this->is_login )
		{
			$post_data = $this->input->post(NULL,true);
			if( $post_data )
			{
				$msg = $this->user->setpasswd( $post_data, $this->uname );
				$this->data['msg'] = $msg;
			}

			//--------------------
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

}
