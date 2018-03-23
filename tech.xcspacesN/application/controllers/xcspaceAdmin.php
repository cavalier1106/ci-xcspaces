<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class xcspaceAdmin extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'admin/index';

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
			/* 只有 xcsapcesAdmin 这个用户才可以 登陆后台 */
			/*$userRoot = array('xcsapcesAdmin', 'liufengsheng', 'xcspaces');
			if( !in_array($this->uname, $userRoot) )
			{
				echo "$this->uname : 您没有权限访问后台!";
				// exit();
			}*/

			//获取用户信息
			$this->data['user_info'] = getUserInfo( $this->uname );

			if($this->data['user_info']['user_type'] != 2)
			{
				echo "$this->uname : 您没有权限访问后台!";
				exit();
			}
			
			//用户头像信息
			$this->data['avatar_arr'] = json_decode( unserialize( $this->data['user_info']['user_avatar'] ), true );
			//提醒系统
			$this->data['notifications'] = get_notifications( $this->uname );
		}
		else
		{
			// 没有登陆不可以访问
			header( "location: " . $this->data['base_url'] );
		}

		$this->data['is_login'] = $this->is_login;
	}
	
	function model()
	{
		$this->load->model('admin/lfs_ajax','adminAjax');
		$this->load->model('admin/lfs_nodes','nodes');
		$this->load->model('admin/lfs_topic','topic');
	}

	function library()
	{
		// $this->load->library('gettreeid');
	}
	
	/* 首页 */
	public function index()
	{
		$this->data['content'] = 'index';
		$nodes = $this->nodes->get_nodes();
		$this->data['nodes'] = $nodes;
		// echo "<pre>";var_dump($nodes);exit;

		$this->load->view($this->default_tpl,$this->data);
	}

	/* 话题编辑 */
	public function TopicsEdit( $tid = 0 )
	{
		$this->data['content'] = 'TopicsEdit';
		$this->data['data'] = $this->topic->get_one_topic_info( $tid );

		$post_data = $this->input->post(NULL,true);
		if( $post_data )
		{
			$msg = $this->topic->edit_topic( $post_data );
			// echo "<pre>";var_dump($msg);exit;
			$this->data['msg'] = $msg; 
		}

		//--------------------------

		$this->load->view($this->default_tpl,$this->data);
	}

	/* 空间编辑 */
	public function spaceEdit( $nid = 0 )
	{
		$this->data['content'] = 'spaceEdit';
		$this->data['data'] = $this->nodes->get_one_space_info( $nid );

		$post_data = $this->input->post(NULL,true);
		if( $post_data )
		{
			$msg = $this->nodes->edit_space( $post_data );
			// echo "<pre>";var_dump($msg);exit;
			$this->data['msg'] = $msg; 
		}

		//--------------------------

		$this->load->view($this->default_tpl,$this->data);
	}

	/* 没有审核话题 */
	public function TopicNoAudit( $page = 1 )
	{
		$this->data['content'] = 'TopicNoAudit';
		$this->data['is_login'] = $this->is_login;

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 空间相关话题分页显示 
		 */
		$data = $this->topic->get_topic_no_audit_total();

		$setting =array(
			'base_url' => 'xcspaceAdmin/TopicNoAudit',
			'total_rows' => $data['nums'],
			'per_page' => 100,
			'uri_segment' => 3
		);

		//空间话题总数
		$this->data['titles_count'] = $data['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['data'] = $this->topic->get_topic_no_audit($page, $setting['per_page']);
		
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

	/* 没有审核话题回复 */
	public function TopicReplyNoAudit( $tid = 0, $page = 1 )
	{
		$this->data['content'] = 'TopicReplyNoAudit';
		$this->data['is_login'] = $this->is_login;
		$this->data['tid'] = $tid;

		if( isset( $_GET['p'] ) )
		{
			$page = (int)$_GET['p'];
			if( $page == 0 ) $page = 1;
		}

		/* 
		 * 空间相关话题分页显示 
		 */
		$data = $this->topic->get_topic_reply_no_audit_total( $tid );

		$setting =array(
			'base_url' => 'xcspaceAdmin/TopicReplyNoAudit/' . $tid,
			'total_rows' => $data['nums'],
			'per_page' => 100,
			'uri_segment' => 4
		);

		//空间话题总数
		$this->data['titles_count'] = $data['nums'];
		$this->data['page'] = pagination($setting);
		$this->data['data'] = $this->topic->get_topic_reply_no_audit($tid, $page, $setting['per_page']);
		
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
	 * 增加次级話題数据
	 */
	public function AddTopicsMenu(){

		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->add_topic( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題增加成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題增加失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

	/* 
	 * 增加顶级話題数据
	 */
	public function AddParentTopicsMenu(){

		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->add_top_topic( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題增加成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題增加失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

	/* 
	 * 删除顶级話題数据
	 */
	public function DelTopicsMenu(){

		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->del_topic( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題删除成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題删除失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

	/* 
	 * 保存顶级話題数据
	 */
	public function SaveTopicsMenu(){

		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->save_topic( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題保存成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題保存失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

	/* 
	 * 修改話題状态
	 */
	public function modifyTopicsStatus()
	{
		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->modifyTopicsStatus( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題保存成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題保存失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

	/* 
	 * 修改話題回复状态
	 */
	public function modifyTopicsReplyStatus()
	{
		$post = $this->input->post(NULL,true);

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->adminAjax->modifyTopicsReplyStatus( $post );
			
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //話題保存成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//話題保存失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}

}
