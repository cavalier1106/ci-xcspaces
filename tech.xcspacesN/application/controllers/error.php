<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends CI_Controller {

	public $data = array();
	private $default_tpl = 'xcspace/error_404';

	function __construct()
	{
		parent::__construct();
		$this->load->library('parser');
		$this->data['base_url'] = $this->config->item('base_url');
	}

	/* 更多话题空间 */
	public function index()
	{
		ob_start();
		include(APPPATH.'views/xcspace/error_404.php');
		$buffer = ob_get_contents();
		ob_end_clean();

		echo $buffer;
		exit;

		// $this->load->view($this->default_tpl,$this->data);
	}
}
