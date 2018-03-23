<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class techApi extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('parser');
		$this->load->helper('string');

		// session_start();
		if($this->input->get('token') !== 'XCSPACE100YEAR'){
			exit('params error.');
		}

		$this->load->model('front/lfs_ajax','op_ajax');
		$this->load->model('front/lfs_loginreg','loginreg');
	}

	/* 
	 * 统一输出信息方法
	 */
	public function echo_json_encode($data=array()){

		if( $data )
		{
			echo json_encode( array('res' => 200, 'msg' => 'success', 'data' => $data) );
		}
		else
		{
			echo json_encode( array('res' => 0, 'msg' => 'fail') );
		}

		exit();
	}
	
	/* 
	 * Get
	 */
	public function getTechTopic(){

		//判断用户是否登录(未处理)
		//判断用户名跟用户id是否一致(未处理)
		//http://tech.xcspaces.com/getTechTopic?page=1&pageSize=1

		$res = $this->op_ajax->get_tech_topic();
		$this->echo_json_encode($res);

	}

	/* 
	 * 自动生成 -- 用户账号
	 */
	public function autoCreateAccount(){

		//http://tech.xcspaces.com/techApi/autoCreateAccount?token=?

		$email = array('@gmail.com', '@hotmail.com', '@yahoo.com', '@mail.ru', '@aol.com', '@hotmail.co.uk', '@live.com', '@msn.com', '@yandex.ru', '@hotmail.fr');

		$res_arr = array();
		foreach ($email as $key => $value) {
			$post_data = array(
				'uname' => 'space'.rand(100000, 300000000),
				'passwd' => 'kn123456',
				'code' => 0,
				'email' => rand(1000000, 300000000) . $value,
				'autoCreatAccount' => 1,
			);
			$res = $this->loginreg->register_user($post_data);
			$res_arr[] = $res;
		}
		
		$this->echo_json_encode($res_arr);

	}

	/* 
	 * Set
	 */
	public function setUsersToFiles(){

		//http://tech.xcspaces.com/setUsersToFiles

		$res = $this->op_ajax->set_user_to_file();
		$this->echo_json_encode($res);

	}

	/* 
	 * Get
	 */
	public function getUsersToFiles(){

		//http://tech.xcspaces.com/getUsersToFiles

		$res = $this->op_ajax->get_user_to_file();
		$this->echo_json_encode($res);

	}

}
