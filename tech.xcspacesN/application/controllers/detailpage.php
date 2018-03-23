<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detailpage extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'xcspace/index';

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('parser');
		$this->model();
		$this->library();
		$this->load->database();
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
		 * http://xcspace.com/?tab=business
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

		$nodes = $this->nodes->get_nodes();
		$this->data['nodes'] = $nodes;
		
		// $this->data['znodes'] = $nodes[$tab];
		$this->data['tab'] = $tab;

		/* SEO处理 */
		$this->data['title'] = config_item('title');
		$this->data['keyworks'] = config_item('keyworks');
		$this->data['description'] = config_item('description');

		$this->data['host_xcspaces'] = config_item('HOST_XCSPACES');

		// var_dump($this->session->all_userdata());exit;
	}
	
	function model()
	{
		$this->load->model('front/lfs_nodes','nodes');
	}
	function library()
	{
		// $this->load->library('gettreeid');
	}
	
	/* about */
	public function index()
	{
		$this->data['content'] = 'about';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* faq */
	public function faq()
	{
		$this->data['content'] = 'faq';
		$this->load->view($this->default_tpl,$this->data);
	}
	
	/* 投放广告 */
	public function advertise()
	{
		$this->data['content'] = 'advertise';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 联系我们 */
	public function contact()
	{
		$this->data['content'] = 'contact';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 我们的愿景 */
	public function wish()
	{
		$this->data['content'] = 'wish';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 加入我们 */
	public function join()
	{
		$this->data['content'] = 'join';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 分享酷站 */
	public function sharewebsite()
	{
		$this->data['content'] = 'sharewebsite';

		// if( !$this->redis->exists('sharewebsite') )
		// {
			$sql = "select distinct type, type_name from xcspace_share_website";
			$types = $this->db->query($sql)->result_array();

			$out_data = array();
			foreach ($types as $key => $value) 
			{
				$sql = "select * from xcspace_share_website where type={$value['type']}";
				$ds = $this->db->query($sql)->result_array();
				$out_data[$key]['type_name'] = $value['type_name'];
				$out_data[$key]['data'] = $ds;
			}
			// $this->data['websites'] = $out_data;
		// 	$this->redis->set('sharewebsite', serialize($out_data), 3600);
		// }
		// else
		// {
		// 	$this->data['websites'] = unserialize( $this->redis->get('sharewebsite') );
		// }
		
		// echo "<pre>";var_dump($out_data);exit;
			
		$str = website_item( $out_data );
		file_put_contents('./application/views/xcspace/staticShareWebsite.php', $str);

		$this->load->view($this->default_tpl, $this->data);
	}

	/* 分享酷站 */
	public function get_sharewebsite()
	{
		// echo "<pre>";print_r(json_decode($_POST['d'], true));exit;
		$d = json_decode($_POST['d'], true);

		// foreach($d as $k => $v)
		// {
		// 	foreach($v['sites'] as $k2 => $v2)
		// 	{
		// 		$data = array(
		// 				'src' => $v2['src'],
		// 				'href' => $v2['href'],
		// 				'title' => $v2['text'],
		// 				'text' => $v2['title'],
		// 				'type_name' => $v['heading'],
		// 				'type' => $k,
		// 				'time' => time(),
		// 			);
		// 		$this->db->insert('share_website', $data);
		// 	}
		// }
	}
	
	/* 版本历程 */
	public function version()
	{
		$this->data['content'] = 'version2';
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 威望页面 */
	public function prestige()
	{
		$this->data['content'] = 'prestige';
		$this->load->view($this->default_tpl,$this->data);
	}

}
