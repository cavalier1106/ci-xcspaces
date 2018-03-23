<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OpAjax extends CI_Controller {
	
	private $is_login = false;

	function __construct()
	{
		parent::__construct();
		$this->load->model('front/lfs_ajax','op_ajax');
		//启动 redis
		$redis_conf = $this->config->item('redis');
		$this->load->driver('cache', array('adapter' => 'redis', 'config' => $redis_conf['xcspace']), 'redis');
		/*$this->uname = $this->input->cookie('uname');
		if( !empty( $this->uname ) )
		{
			//判断redis是否存在用户名键值,存在获取用户所有信息
			if( unserialize( $this->redis->get("XCSPACES_USER[{$this->uname}]") ) )
			{
				//获取用户信息
				$this->data['user_info'] = getUserInfo( $this->uname );
				$this->is_login = true;
			}
		}*/
	}

	/* 
	 * 发布主题数据
	 */
/*	public function publishTopic(){

		$post = $this->input->post();

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		if( $this->is_login )
		{
			$res = $this->op_ajax->set_publish_topic( $post );
			if( $res['res'] )
			{
				echo json_encode( array('res' => 1, 'msg' => $res['msg']) ); //主题发布成功！
			}
			else
			{
				echo json_encode( array('res' => 0, 'msg' => $res['msg']) );//主题发布失败！
			}
		}
		else
		{
			echo json_encode( array('res' => 2, 'msg' => '用户没有登录') );
		}
		
		exit();
	}*/

	/* 
	 * Get
	 */
	public function getTechTopic(){

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)

		$res = $this->op_ajax->get_tech_topic();
		if( $res )
		{
			echo json_encode( array('res' => 200, 'msg' => 'success', 'data' => $res) ); //主题发布成功！
		}
		else
		{
			echo json_encode( array('res' => 0, 'msg' => 'fail') );//主题发布失败！
		}
		
		exit();
	}
	
}
