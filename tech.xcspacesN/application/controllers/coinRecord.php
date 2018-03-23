<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CoinRecord extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'xcspace/index';
	// private $is_login = false;
	// private $uname = false;
	//操作类型:0+初始资本,1+每日登录奖励,2+每日活跃度奖励,3-创建主题,4-创建回复,5+主题回复收益,6+注册推荐收益,7-发送谢意
	public $op_type = array( 
		0 => '初始资本',
		1 => '每日登录奖励',
		2 => '每日活跃度奖励',
		3 => '创建主题',
		4 => '创建回复',
		5 => '主题回复收益',
		6 => '注册推荐收益',
		7 => '发送谢意',
		8 => '创建笔记',
	);

	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('parser');
		$this->load->helper('string');
		$this->model();
		$this->library();
		$this->data['base_url'] = $this->config->item('base_url');

		//每日登录奖励是否获取
		$this->data['dailyCoinIsGet'] = 'logout';

		//判断redis是否存在用户名键值,存在获取用户所有信息
		if( $this->is_login )
		{
			//获取用户信息
			$this->data['user_info'] = getUserInfo( $this->uname );
			//用户头像信息
			$this->data['avatar_arr'] = json_decode( unserialize( $this->data['user_info']['user_avatar'] ), true );
			//提醒系统
			$this->data['notifications'] = get_notifications( $this->uname );
			//每日登录奖励是否获取
			$this->data['dailyCoinIsGet'] = dailyCoinIsGet( $this->uname );
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
			if( !isset( $nodes[$tab] ) )
			{
				error_404();
			}
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
		$this->load->model('front/lfs_coin_record','coin_record');
	}

	function library()
	{
		// $this->load->library('gettreeid');
	}
	
	/* 首页 */
	public function index()
	{
		$this->daily();
	}

	/* 
	 * 用户每日登录奖励页面 
	 */
	public function daily()
	{
		$get_data = $this->input->get();

		if( $this->is_login )
		{
			//是否收藏 --------------------
			// $users_arr = explode( ',', $this->data['user_info']['users_str'] );
			// if( in_array($get_data['uid'], $users_arr) )
			// {
			// 	$this->data['is_favorite'] = true;
			// }
			// else
			// {
			// 	$this->data['is_favorite'] = false;
			// }
			
			//获取用户信息
			$this->data['one_user_info'] = getUserInfo( $this->uname );

			/* 
			 * 可以先检查uid是否有效
			 */

			$this->data['content'] = 'daily';
			/* 
			 * 置顶广告显示处理 
			 */
			$znode_name = 'tech';
			$ads = $this->ads->get_top_ads( $znode_name );
			$this->data['ads'] = $ads;
			
			$uid = $this->data['user_info']['id']; 
			// echo "<pre>";var_dump(empty( $get_data ));exit;

			if( !empty( $get_data ) )
			{
				/* 
				 * 用户每日登录奖励处理 
				 */
				$res = $this->coin_record->get_daily_coin( $uid );
				$this->data['msg'] = $res;
			}
			else
			{
				/* 
				 * 领取前先判断今天是否已领取 
				 */
				$this->data['is_get_daily_coin'] = $this->coin_record->is_get_daily_coin( $uid );
			}

			//--------------------

			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}	
		
	}

	/* 
	 * 用户奖励记录页面 
	 */
	public function myCoin( $uid = 0, $page = 1 )
	{

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}
		
		// if( $this->is_login )
		// {
			$this->data['content'] = 'myCoin';
			/* 
			 * 置顶广告显示处理 
			 */
			$znode_name = 'tech';
			$ads = $this->ads->get_top_ads( $znode_name );
			$this->data['ads'] = $ads;

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
			
			//获取用户信息
			$this->data['one_user_info'] = getUserInfo2( $uid );

			/* 
			 * 可以先检查uid是否有效
			 */
			if( $uid != 0 )
			{
				$this->data['op_type'] = $this->op_type;
				/* 
				 *  奖励记录总数
				 */
				$coin_record_count = $this->coin_record->get_coin_record_count( $uid );
				$this->data['coin_record_count'] = $coin_record_count;

				$setting =array(
					'base_url' => 'coinRecord/myCoin/' . $uid,
					'per_page' => 15,
					'uri_segment' => 4
				);

				/* 
				 * 用户奖励记录列表
				 */
				$coin_records = $this->coin_record->get_coin_records_findList( $uid, $page, $setting['per_page']);
				$setting['total_rows'] = $coin_record_count;
				//奖励记录总数
				$this->data['count'] = $coin_record_count;
				$this->data['page'] = pagination($setting);
				$this->data['datas'] = $coin_records['data'];
				//当前页数
				$this->data['current_page'] = $page;
				//总页数
				if( $coin_record_count % $setting['per_page'] == 0 )
				{
					$this->data['page_total'] = $coin_record_count / $setting['per_page'];
				}
				else
				{
					$this->data['page_total'] = (int)($coin_record_count / $setting['per_page']) + 1;
				}

				//--------------------

				$this->load->view($this->default_tpl, $this->data);
			}
			else
			{
				header( "location: " . $this->data['base_url'] );
			}
			
		// }
		// else
		// {
		// 	header( "location: " . $this->data['base_url'] );
		// }	
		
	}
	

}
