<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {

	public $is_login = false;
	public $uname = null;

	function __construct()
	{
		parent::__construct();

		//还原 session 场景
		if($_REQUEST['PHPSESSID'] !== "" && $_REQUEST["PHPSESSID"] !== "undefined") 
		{
			@session_id($_REQUEST['PHPSESSID']);
		}
		session_start();
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}
}
