<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class xcspace extends CI_Controller {
	
	public $data = array();
	private $default_tpl = 'xcspace/index';
	// private $is_login = false;
	// private $uname = null;
	// private $redis_conf;
	//检测$t是否有效 -- 用户跟空间话题关注用户信息表
	private $favorite = array('titles_str', 'users_str', 'nodes_str', 'ignore_topic_str', 'thanks_topic_str', 'thanks_reply_topic_str');

	function __construct()
	{
		parent::__construct();

		header("Content-type: text/html; charset=utf-8");

		$this->load->helper('url');
		$this->load->library('parser');
		$this->load->helper('string');
		$this->model();
		$this->library();
		$this->data['base_url'] = $this->config->item('base_url');

		//每日登录奖励是否获取
		$this->data['dailyCoinIsGet'] = 'logout';

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

		// 获取用户威望排名信息
		$this->data['UserCoinRank'] = getUserCoinRank();

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
  		 * 最新话题/热门话题
		 */
		if(isset($_GET['t']))
		{
			$t = $_GET['t'];
		}
		else
		{
			$t = 'new';
		}

		$this->data['t'] = $t;

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
		
		// echo "<pre>";var_dump($this->data['nodes']);exit;

		include_once( './weiboApi/saetv2.ex.class.php' );
		$o = new SaeTOAuthV2( config_item('WB_AKEY'), config_item('WB_SKEY') );
		$this->data['code_url'] = $o->getAuthorizeURL( config_item('WB_CALLBACK_URL') );
		// echo $this->data['code_url'];exit;
	}
	
	function model()
	{
		$this->load->model('front/lfs_nodes','nodes');
		$this->load->model('front/lfs_titles','titles');
		$this->load->model('front/lfs_user','user');
		$this->load->model('front/lfs_ads','ads');
		$this->load->model('front/Lfs_topic_reply','replys');
		// $this->load->model('front/Lfs_topic_clicks_details','title_statistic');
		// $this->titles->set_id_to_hash();exit;
		
		/* 
		 * 今日热议话题显示处理 
		 */
		$hot_titles = $this->titles->get_hot_titles();
		$this->data['hot_titles'] = $hot_titles;
	}

	function library()
	{
		// $this->load->library('lib_redis', $this->redis_conf['xcspace2']);
	}
	
	/* 首页 */
	public function index()
	{
		$this->data['content'] = 'index';

		/* SEO处理 */
		if( $this->data['tab'] != 'home' )
		{
			$this->data['title'] = $this->data['nodes'][$this->data['tab']]['fid']['title'];
			$this->data['keyworks'] = $this->data['nodes'][$this->data['tab']]['fid']['keyworks'];
			$this->data['description'] = $this->data['nodes'][$this->data['tab']]['fid']['description'];
		}

		// $nodes = $this->nodes->get_nodes();
		// $this->data['nodes'] = $nodes;
		
		// $this->data['znodes'] = $nodes[$tab];
		// $this->data['tab'] = $tab;

		/* 
		 * 最新话题显示处理 
		 */
		$titles = $this->titles->get_node_titles( $this->data['tab'], $this->data['t'] );
		$this->data['titles'] = $titles;
		// echo "<pre>";var_dump($titles);exit;

		/* 
		 * 登录状态处理 
		 */

		/* 
		 * 今日热议话题显示处理 
		 */
		// $hot_titles = $this->titles->get_hot_titles();
		// $this->data['hot_titles'] = $hot_titles;

		/* 
		 * 置顶广告显示处理
		 */
		$ads = $this->ads->get_top_ads();
		$this->data['ads'] = $ads;

		/* 
		 * 社区运行状况处理 
		 */

		/* 
		 * 今日热点标签处理 
		 */
		$hot_nodes = $this->nodes->get_hot_nodes();
		$this->data['hot_nodes'] = $hot_nodes;
		/* 
		 * 最近新加标签处理 
		 */
		$new_nodes = $this->nodes->get_new_nodes();
		$this->data['new_nodes'] = $new_nodes;

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 更多话题空间 */
	public function tnodes()
	{
		$this->data['content'] = 'tabs';

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
			$tab = 'tech';
		}

		/* 
		 * 置顶广告显示处理
		 */
		$ads = $this->ads->get_top_ads( $tab );
		$this->data['ads'] = $ads;

		/* 
		 * 社区运行状况处理 
		 */

		//----------------------------------------

		$this->load->view($this->default_tpl,$this->data);
	}

	/* 
	 * 显示更多话题列表页面 
	 */
	public function more( $t = 'new', $page = 1 )
	{
		$this->data['content'] = 'moreTopics';
		$this->data['t'] = $t;

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 置顶广告显示处理 
		 */
		$znode_name = 'tech';
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;

		/* 
		 *  更多话题列表总数
		 */
		$more_topics_count = $this->titles->more_topics_count();
		$this->data['more_topics_count'] = $more_topics_count;

		/* 
		 * 更多话题列表
		 */
		$setting =array(
			'base_url' => 'more/' . $t,
			'per_page' => 20,
			'uri_segment' => 4
		);
		$more_topics = $this->titles->more_topics( $page, $setting, $this->data['t'] );
		$setting['total_rows'] = $more_topics_count;
		//我的收藏话题总数
		$this->data['titles_count'] = $more_topics_count;
		$this->data['page'] = pagination($setting);
		$this->data['titles'] = $more_topics['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $more_topics_count % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $more_topics_count / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($more_topics_count / $setting['per_page']) + 1;
		}

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 子空间 */
	public function go( $znode_name = "tech", $page = 1 )
	{
		$this->data['content'] = 'go';
		$this->data['znode_name'] = $znode_name;
		$this->data['is_login'] = $this->is_login;

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		//发布话题
		if( $this->is_login )
		{
			$post_data = $this->input->post(NULL,true);
			if( $post_data['nid'] && $post_data['content'] && $post_data['title'] )
			{
				//保存话题数据
				$msg = $this->titles->topics_save( $post_data, $this->data['user_info'] );
				$this->data['msg'] = $msg;
			}
		}

		/* 
		 * 空间相关话题分页显示 
		 */
		$data = $this->titles->topics_total( $znode_name );
		$setting =array(
			'base_url' => 'go/' . $znode_name,
			'total_rows' => $data['nums'],
			'per_page' => 20,
			'uri_segment' => 4
		);

		//空间话题总数
		$this->data['titles_count'] = $data['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['titles'] = $this->titles->findlist($page, $setting['per_page'], $data['zid'], $this->data['t']);
		// $this->data['titles_num'] = ( $page - 1 ) * $setting['per_page'] + 1;
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $data['nums'] % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $data['nums'] / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($data['nums'] / $setting['per_page']) + 1;
		}

		/* 
		 * 置顶广告显示处理 
		 */
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;
		
		/* 
		 * 子空间关联广告 
		 */
		$ads_list = $this->ads->get_ads( $znode_name );
		$this->data['ads_list'] = $ads_list;
		// echo "<pre>";var_dump($ads_list);exit;
		/* 
		 * 子空间关联空间
		 */
		$relate_nodes = $this->nodes->get_relate_nodes( $znode_name );
		$this->data['relate_nodes'] = $relate_nodes;

		/* 
		 * 当前子空间信息
		 */
		$node_info = $this->nodes->get_node_info( $znode_name );
		$this->data['node_info'] = $node_info;
		// var_dump($node_info);exit;
		$this->data['tab'] = $node_info['f_tab_name'];

		/* SEO处理 */
		$this->data['title'] = $node_info['name'];
		$this->data['keyworks'] = $node_info['keyworks'];
		$this->data['description'] = $node_info['description'];

		//重置子话题信息 -------------------------------***********************************************
		// $this->data['znodes'] = $this->data['nodes'][$node_info['f_tab_name']];

		// 登录数据	
		if( $this->is_login )
		{
			//是否收藏 -------------------- 还有问题 键值可能为0
			$nodes_arr = explode( ',', $this->data['user_info']['nodes_str'] );
			if( in_array($node_info['zid'], $nodes_arr) )
			{
				$this->data['is_favorite'] = true;
			}
			else
			{
				$this->data['is_favorite'] = false;
			}
		}

		//--------------------------

		$this->load->view($this->default_tpl,$this->data);
	}

	public function go_ajax()
	{
		//发布话题
		if( $this->is_login )
		{
			$post_data = $this->input->post();
			//特殊字符要处理
			$post_data['nid'] = addslashes( $post_data['nid'] );
			$post_data['title'] = addslashes( $post_data['title'] );
			$post_data['content'] = addslashes( $post_data['content'] );

			// echo json_encode($post_data);exit;

			if( $post_data['nid'] && $post_data['content'] && $post_data['title'] )
			{
				//保存话题数据
				$msg = $this->titles->topics_save( $post_data, $this->data['user_info'] );
				$msg['content'] = $post_data['content'];
				echo json_encode( $msg );
				exit();
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/* 子空间 */
	public function search ( $page = 1 )
	{
		$this->data['content'] = 'search';

		if( $s = $this->input->get('s') )
		{
			$search_content = $s;
		}
		else
		{
			$search_content = '';
		}

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		$this->data['s'] = $search_content;

		// echo $search_content ;exit;

		/* 
		 * 空间相关话题分页显示 
		 */
		$data = $this->titles->search_topics_total( $search_content );
		$setting =array(
			'base_url' => 'search',
			'total_rows' => $data['nums'],
			'per_page' => 20,
			'uri_segment' => 3
		);

		//空间话题总数
		$this->data['counts'] = $data['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $this->titles->search_topics_findlist($page, $setting['per_page'], $search_content);
		
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $data['nums'] % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $data['nums'] / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($data['nums'] / $setting['per_page']) + 1;
		}
	
		//--------------------------

		$this->load->view($this->default_tpl,$this->data);
	}
	
	/* 
	 * 详细话题页面 
	 */
	public function detail( $hashId = 0 )
	{
		// $tid = (int)$tid;
		$tid = topic_hashId_to_id( $hashId );

		$this->data['content'] = 'detail';
		$this->data['hashId'] = $hashId;
		$this->data['REQUEST_URI'] = '/detail/'.$hashId;

		if( isset( $_GET['type'] ) )
		{
			if( $_GET['type'] == 'coin' )$this->data['order_type'] = 'coin';
			if( $_GET['type'] == 'time' )$this->data['order_type'] = 'time';
		}
		else
		{
			$this->data['order_type'] = 'coin';
		}

		/* 
		 *  话题页面点击数统计
		 *  话题页面点击数统计---加入redis ( 减轻服务器 )
		 */
		$this->load->model('front/Lfs_topic_clicks_details','topic_clicks_details');
		$this->topic_clicks_details->set_topic_clicks_details( $tid, $this->uname );

		/* 
		 * ONE话题信息 
		 */
		$one_topic = $this->titles->get_one_topic( $tid );

		if($one_topic['topic_type'] == 2){
			//如果不是自己网站的文章类型就就跳转
			Header("HTTP/1.1 303 See Other"); 
			header( "location: " . stripslashes($one_topic['out_link']) );
			exit();
		}

		$this->data['one_topic'] = $one_topic;

		/* 
		 * 置顶广告显示处理 
		 */
		$znode_name = $this->nodes->get_znode_name( $tid );
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;

		/* 
		 * 话题关联图片广告
		 */
		$ads_list = $this->ads->get_topic_ads( $tid );
		$this->data['ads_list'] = $ads_list;

		/* 
		 * 话题关联文字广告(淘宝,京东,亚马逊等网址链接)
		 */

		/* 
		 * ONE话题空间面包屑信息 
		 */
		$one_topic_crumb = $this->nodes->get_one_topic_crumb( $tid );
		$this->data['one_topic_crumb'] = $one_topic_crumb;
		$this->data['tab'] = $one_topic_crumb['f_tab_name'];
		$this->data['ztab'] = $one_topic_crumb['z_tab_name'];

		// echo $one_topic['content'];exit;
		/* SEO处理 */
		$this->data['title'] = $one_topic['title'];
		$this->data['keyworks'] = $one_topic['keyworks'];
		$this->data['description'] = $one_topic['description'];
		
		/* 
		 * ONE话题相关回复
		 */
		$topic_reply = $this->replys->get_topic_reply( $tid );
		$this->data['topic_reply'] = $topic_reply;

		//判断此话题是否是当前登录用户创建的 --------------------
		$this->data['is_current_user_create_topic'] = false;

		// 登录数据	
		if( $this->is_login )
		{
			//判断此话题是否是当前登录用户创建的 --------------------
			$res = $this->titles->is_current_user_create_topic( $tid, $this->data['user_info']['id'] );
			if( $res )
			{
				$this->data['is_current_user_create_topic'] = true;
			}
			

			//是否收藏 --------------------
			$titles_arr = explode( ',', $this->data['user_info']['titles_str'] );
			if( in_array($tid, $titles_arr) )
			{
				$this->data['is_favorite'] = true;
			}
			else
			{
				$this->data['is_favorite'] = false;
			}
			
			//是否感谢过该话题 -------------------- 
			$thanks_topic_arr = explode( ',', $this->data['user_info']['thanks_topic_str'] );
			if( in_array($tid, $thanks_topic_arr) )
			{
				$this->data['is_thanks_topic'] = true;
			}
			else
			{
				$this->data['is_thanks_topic'] = false;
			}

			$this->data['uname'] = $this->uname;
		}

		// $post_data = $this->input->post(NULL,true);
		
		// if( !empty( $post_data ) )
		// {
		// 	//1判断用户是否登录
		// 	$uname = $this->input->post('uname');
		// 	//2保存回复数据
		// 	if( $this->is_login )
		// 	{
		// 		$this->replys->set_topic_reply( $post_data );
		// 	}
		// }

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	function detail_ajax()
	{
		if( $this->is_login )
		{
			// $this ->security->csrf_verify(); //csrf检查
			$post_data = $this->input->post();

			if( !empty( $post_data ) )
			{
				//特殊字符要处理
				$post_data['content'] = addslashes( $post_data['content'] );
				//保存回复数据
				$msg = $this->replys->set_topic_reply( $post_data );
				echo json_encode( $msg );
				exit();
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	function coin_ajax()
	{
		if( $this->is_login )
		{
			$coin = (int)$this->data['user_info']['coin'];
			if( $coin > 5 )
			{
				$msg = array('code' => 0, 'msg' => '威望值满足感谢');
				echo json_encode( $msg );
				exit();
			}
			else
			{
				$msg = array('code' => 1, 'msg' => '威望值不足,不能感谢');
				echo json_encode( $msg );
				exit();
			}
		}
		else
		{
			$msg = array('code' => 1, 'msg' => '没有登陆');
			echo json_encode( $msg );
			exit();
		}
	}

	/* 
	 * 详细笔记页面 
	 */
	public function noteDetail( $hashId = 0 )
	{
		// $nId = (int)$nId;
		$nId = notepad_hashId_to_nId( $hashId );

		$this->data['content'] = 'noteDetail';
		$this->data['nId'] = $nId;

		/* 
		 *  笔记页面点击数统计
		 *  笔记页面点击数统计---加入redis ( 减轻服务器 )
		 */
		// $this->load->model('front/Lfs_topic_clicks_details','topic_clicks_details');
		// $this->topic_clicks_details->set_topic_clicks_details( $nId, $this->uname );

		/* 
		 * 置顶广告显示处理 
		 */
		// $znode_name = $this->nodes->get_znode_name( $nId );
		// $ads = $this->ads->get_top_ads( $znode_name );
		// $this->data['ads'] = $ads;

		/* 
		 * 话题关联图片广告
		 */
		// $ads_list = $this->ads->get_topic_ads( $nId );
		// $this->data['ads_list'] = $ads_list;

		/* 
		 * 话题关联文字广告(淘宝,京东,亚马逊等网址链接)
		 */

		/* 
		 * ONE笔记空间面包屑信息 
		 */
		// $one_topic_crumb = $this->nodes->get_one_topic_crumb( $nId );
		// $this->data['one_topic_crumb'] = $one_topic_crumb;
		// $this->data['tab'] = $one_topic_crumb['f_tab_name'];
		// $this->data['ztab'] = $one_topic_crumb['z_tab_name'];

		/* 
		 * ONE笔记信息 
		 */
		$this->load->model('front/lfs_notepad','notepad');
		$one_notepad = $this->notepad->get_one_notepad( $nId );
		$this->data['one_notepad'] = $one_notepad;

		/* SEO处理 */
		$this->data['title'] = $one_notepad['title'];
		$this->data['keyworks'] = $one_notepad['keyworks'];
		$this->data['description'] = $one_notepad['description'];

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 
	 * 创建话题页面 
	 */
	public function createNewTitle( $zid = 0 )
	{
		$this->data['content'] = 'createNewTitle';
		$this->data['zid'] = $zid;
		$this->data['is_login'] = $this->is_login;

		// 测试登录数据	
		if( $this->is_login )
		{
			$znode_name = 'tech';

			/* 
			 * 置顶广告显示处理 
			 */
			$ads = $this->ads->get_top_ads( $znode_name );
			$this->data['ads'] = $ads;
			
			/* 
			 * 获取2级空间 
			 */
			// $znodes = $this->nodes->get_znodes_new();
			$znodes = $this->nodes->get_znodes_list();
			$this->data['all_nodes'] = $znodes;
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'].'loginreg' );
		}

	}

	/* 
	 * 创建用户笔记页面 
	 */
	public function createNotepad()
	{
		$this->data['content'] = 'createNotepad';
		$this->data['is_login'] = $this->is_login;

		// 测试登录数据	
		if( $this->is_login )
		{
			// $znode_name = 'business';

			/* 
			 * 置顶广告显示处理 
			 */
			// $ads = $this->ads->get_top_ads( $znode_name );
			// $this->data['ads'] = $ads;
			
			/* 
			 * 获取2级空间 
			 */
			// $znodes = $this->nodes->get_znodes();
			// $this->data['all_nodes'] = $znodes;
			
			$this->load->view($this->default_tpl, $this->data);
		}
		else
		{
			header( "location: " . $this->data['base_url'].'loginreg' );
		}
	}

	public function createNotepad_ajax()
	{
		if( $this->is_login )
		{
			// $post_data = $this->input->post(NULL,true); 这种写法会不能保存图片 得优化
			if( $post_data = $this->input->post() )
			{
				//特殊字符要处理
				$post_data['content'] = addslashes( $post_data['content'] );

				//话题保存成功
				$this->load->model('front/lfs_notepad','notepad');
				$msg = $this->notepad->set_notepad( $post_data, $this->data['user_info'] );
				
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
	 * 反馈页面 
	 */
	public function feedback()
	{
		$this->load->model('front/Lfs_feedback','feedback');

		$this->data['content'] = 'feedback';

		$post_data = $this->input->post(NULL,true);
		
		if( $post_data )
		{
			//保存反馈
			if( !$this->uname )
			{
				$uname = '';
			}
			$msg = $this->feedback->set_feedback( $this->uname, $post_data );
			$this->data['msg'] = $msg;
		}

		$this->load->view($this->default_tpl, $this->data);
	}

	public function createNewTitle_ajax()
	{
		if( $this->is_login )
		{
			// $post_data = $this->input->post(NULL,true); 这种写法会不能保存图片 得优化
			if( $post_data = $this->input->post() )
			{
				//特殊字符要处理
				$post_data['content'] = addslashes( $post_data['content'] );

				//话题保存成功
				$msg = $this->titles->set_tpoic( $post_data, $this->data['user_info'] );
				
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
	 * 用户显示收藏空间页面 
	 */
	public function nodes( $tid = 0 )
	{
		$this->data['content'] = 'nodes';
		$this->load->view($this->default_tpl, $this->data);
	}

	/* 
	 * 用户显示收藏话题页面 
	 */
	public function collectionNodes( $uid = 1, $page = 1 )
	{
		$this->data['content'] = 'collectionNodes';
		$this->data['is_login'] = $this->is_login;
		$znode_name = 'tech';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 置顶广告显示处理 
		 */
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
		 *  我的话题收藏总数
		 */
		$nodes_count = $this->nodes->collection_nodes_count( $uid );
		$this->data['nodes_count'] = $nodes_count;

		/* 
		 * 我的收藏话题列表
		 */
		$setting =array(
			'base_url' => 'collectionNodes/' . $uid,
			'per_page' => 60,
			'uri_segment' => 4
		);
		$collection_nodes = $this->nodes->collection_nodes( $uid, $page, $setting );
		$setting['total_rows'] = $collection_nodes['nums'];
		//我的收藏话题总数
		$this->data['titles_count'] = $collection_nodes['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $collection_nodes['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $collection_nodes['nums'] % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $collection_nodes['nums'] / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($collection_nodes['nums'] / $setting['per_page']) + 1;
		}

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 
	 * 用户显示收藏话题页面 
	 */
	public function collectionTopics( $uid = 1, $page = 1 )
	{
		$this->data['content'] = 'collectionTopics';
		$this->data['is_login'] = $this->is_login;
		$znode_name = 'tech';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 置顶广告显示处理 
		 */
		$ads = $this->ads->get_top_ads( $znode_name );
		$this->data['ads'] = $ads;

		/* 
		 *  我的话题收藏总数
		 */
		$topics_count = $this->titles->collection_topics_count( $uid );
		$this->data['topics_count'] = $topics_count;

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
		 * 我的收藏话题列表
		 */
		$setting =array(
			'base_url' => 'collectionTopics/' . $uid,
			'per_page' => 15,
			'uri_segment' => 4
		);
		$collection_topics = $this->titles->collection_topics( $uid, $page, $setting );
		$setting['total_rows'] = $collection_topics['nums'];
		//我的收藏话题总数
		$this->data['titles_count'] = $collection_topics['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $collection_topics['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $collection_topics['nums'] % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $collection_topics['nums'] / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($collection_topics['nums'] / $setting['per_page']) + 1;
		}

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 
	 * 显示关注用户话题列表页面 
	 */
	public function focusMemberTopics( $uid = 1, $page = 1 )
	{
		$this->data['content'] = 'focusMemberTopics';
		$this->data['is_login'] = $this->is_login;
		$znode_name = 'tech';

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 置顶广告显示处理 
		 */
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
		 *  我关注用户话题总数
		 */
		$focus_member_topics_count = $this->titles->focus_member_topics_count( $uid );
		$this->data['focus_member_topics_count'] = $focus_member_topics_count;

		/* 
		 * 我的收藏话题列表
		 */
		$setting =array(
			'base_url' => 'focusMemberTopics/' . $uid,
			'per_page' => 15,
			'uri_segment' => 4
		);
		$focus_member_topics = $this->titles->focus_member_topics( $uid, $page, $setting );
		$setting['total_rows'] = $focus_member_topics_count;
		//我的收藏话题总数
		$this->data['titles_count'] = $focus_member_topics_count;
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $focus_member_topics['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $focus_member_topics_count % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $focus_member_topics_count / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($focus_member_topics_count / $setting['per_page']) + 1;
		}

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 
	 * 显示关注用户列表页面 
	 */
	public function focusMember( $uid = 1, $page = 1 )
	{
		$this->data['content'] = 'focusMember';
		$this->data['is_login'] = $this->is_login;

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

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
		 *  我关注用户话题总数
		 */
		$focus_member_count = $this->user->focus_member_count( $uid );
		$this->data['focus_member_count'] = $focus_member_count;

		/* 
		 * 我的收藏话题列表
		 */
		$setting =array(
			'base_url' => 'focusMember/' . $uid,
			'per_page' => 30,
			'uri_segment' => 4
		);
		$focus_member = $this->user->focus_member( $uid, $page, $setting );
		$setting['total_rows'] = $focus_member_count;
		//我的收藏话题总数
		$this->data['titles_count'] = $focus_member_count;
		$this->data['page'] = pagination($setting);
		$this->data['datas'] = $focus_member['data'];
		//当前页数
		$this->data['current_page'] = $page;
		//总页数
		if( $focus_member_count % $setting['per_page'] == 0 )
		{
			$this->data['page_total'] = $focus_member_count / $setting['per_page'];
		}
		else
		{
			$this->data['page_total'] = (int)($focus_member_count / $setting['per_page']) + 1;
		}

		//--------------------------

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 捐赠 */
	public function donation()
	{
		$this->data['content'] = 'donation';
		$this->data['data'] = array();
		$this->load->view($this->default_tpl,$this->data);
	}

	function favorite( $node_and_topic_and_user_id = 0 )
	{
		/* 收藏话题,关注用户,关注空间,话题感谢 */
		// $node_and_topic_and_user_id = (int)$node_and_topic_and_user_id;
		// $hashId = $node_and_topic_and_user_id;
		// $node_and_topic_and_user_id = topic_hashId_to_id( $node_and_topic_and_user_id );

		if( $this->is_login )
		{
			// 以下写法存在问题
			// if( $t = $this->input->get('t') && $create_userid = $this->input->get('create_userid') )
			// $t 结果为 1

			if( $t = $this->input->get('t') )
			{
				$create_userid = $this->input->get('create_userid');

				if( $t == 'titles_str' || $t == 'thanks_topic_str' )
				{
					$hashId = $node_and_topic_and_user_id;
					$node_and_topic_and_user_id = topic_hashId_to_id( $node_and_topic_and_user_id );
				}

				if( in_array($t, $this->favorite) )
				{
					$res = $this->titles->set_topic_favorite( $node_and_topic_and_user_id, $this->data['user_info'], $t, $create_userid );
					if( $t == 'titles_str' )
					{
						header( "location: " . $this->data['base_url'] . "detail/{$hashId}" );
					}
					elseif( $t == 'users_str' )
					{
						header( "location: " . $this->data['base_url'] . "user/memberTopics/{$node_and_topic_and_user_id}" );
					}
					elseif( $t == 'nodes_str' )
					{
						$tab_name = $this->nodes->get_znode_info( $node_and_topic_and_user_id );
						header( "location: " . $this->data['base_url'] . 'go/' . $tab_name);
					}
					elseif( $t == 'thanks_topic_str' )
					{
						header( "location: " . $this->data['base_url'] . "detail/{$hashId}" );
					}
					else
					{
						header( "location: " . $this->data['base_url'] );
					}
				}
				else
				{
					//
				}
			}
			else
			{
				//
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	function thanksReply( $hashId = 0 )
	{
		/* 收藏话题,关注用户,关注空间,话题感谢 */
		// $tid = (int)$tid;
		$tid = topic_hashId_to_id( $hashId );

		if( $this->is_login )
		{
			if( $t = $this->input->get('t') )
			{
				$reply_userid = $this->input->get('reply_userid');
				$rid = $this->input->get('rid');

				if( in_array($t, $this->favorite) )
				{
					$res = $this->titles->set_thanks_reply( $tid, $this->data['user_info'], $t, $reply_userid, $rid );
					if( $t == 'thanks_reply_topic_str' )
					{
						header( "location: " . $this->data['base_url'] . "detail/{$hashId}" );
					}
					else
					{
						header( "location: " . $this->data['base_url'] );
					}
				}
				else
				{
					//
				}
			}
			else
			{
				//
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	function unfavorite( $node_and_topic_and_user_id = 0 )
	{
		/*取消收藏话题,关注用户,关注空间,话题感谢*/
		// $hashId = $node_and_topic_and_user_id;
		// $node_and_topic_and_user_id = topic_hashId_to_id( $node_and_topic_and_user_id );

		if( $this->is_login )
		{
			if( $t = $this->input->get('t') )
			{
				if( in_array($t, $this->favorite) )
				{
					if( $t == 'titles_str' )
					{
						$hashId = $node_and_topic_and_user_id;
						$node_and_topic_and_user_id = topic_hashId_to_id( $node_and_topic_and_user_id );
					}

					$res = $this->titles->set_topic_unfavorite( $node_and_topic_and_user_id, $this->data['user_info'], $t );
					if( $t == 'titles_str' )
					{
						header( "location: " . $this->data['base_url'] . "detail/{$hashId}" );
					}
					elseif( $t == 'users_str' )
					{
						header( "location: " . $this->data['base_url'] . "user/memberTopics/{$node_and_topic_and_user_id}" );
					}
					elseif( $t == 'nodes_str' )
					{
						$tab_name = $this->nodes->get_znode_info( $node_and_topic_and_user_id );
						header( "location: " . $this->data['base_url'] . 'go/' . $tab_name);
					}
					else
					{
						header( "location: " . $this->data['base_url'] );
					}
				}
				else
				{
					//
				}
			}
			else
			{
				//
			}
		}
		else
		{
			header( "location: " . $this->data['base_url'] );
		}
	}

	/*  
	 * 用户删除自己的话题(假删除) status = 4;
	 */
	function delTopic( $tid )
	{
		//这里没有进行
		$tid = (int)$tid;

		if( $this->is_login )
		{
			$res = $this->titles->del_topic( $tid, $this->data['user_info']);
		}
		
		header( "location: " . $this->data['base_url'] );
	
	}

	/*  
	 * 话题图片上传
	 */
	function upload()
	{

		if( $this->is_login )
		{
			if( !empty($_FILES) && $_FILES['wangEditorH5File']['error'] == 0 )
			{
				$this->load->library('lib_image');
				$tempFile = $_FILES['wangEditorH5File']['tmp_name'];
				$orgFile = $_FILES['wangEditorH5File']['name'];

				// echo "<pre>";var_dump($_FILES['wangEditorH5File']);exit;

				$url = dirname( dirname( dirname( __FILE__ ) ) );

				$uploadDir = $url . '/' . $_REQUEST['folder'];
				$backDir = $_REQUEST['folder'];
				if ( !file_exists( $uploadDir ) ) {
				    make_dir( $uploadDir );
				}

				$Y = date('Y');
				$M = date('m');
				$D = date('d');

				if ( !file_exists( $uploadDir . '/' . $Y ) ) {
				    make_dir( $uploadDir . '/' . $Y );
				}
				if ( !file_exists( $uploadDir . '/' . $Y . '/' . $M ) ) {
				    make_dir( $uploadDir . '/' . $Y . '/' . $M );
				}
				if ( !file_exists( $uploadDir . '/' . $Y . '/' . $M . '/' . $D ) ) {
				    make_dir( $uploadDir . '/' . $Y . '/' . $M . '/' . $D );
				}

				$uploadDir = $uploadDir . '/' . $Y . '/' . $M . '/' . $D;
				$backDir = $backDir . '/' . $Y . '/' . $M . '/' . $D;

				if( !$this->uname )
				{
					echo '异常用户，数据报错！';
					exit();
				}

				$imgUrl = $uploadDir . '/' . 'topics_' . $this->uname . '_' . $orgFile;
				$backImgUrl = $backDir . '/' . 'topics_' . $this->uname . '_' . $orgFile;

				$imgArr = getimagesize($tempFile);
				$imgWidth = $imgArr[0];
				$imgHeight = $imgArr[1];

				$this->lib_image->ImageToJPG($tempFile,$imgUrl, $imgWidth, $imgHeight);

				echo $backImgUrl;
				exit();

			}
		}
		
		exit();
	
	}
	
}
