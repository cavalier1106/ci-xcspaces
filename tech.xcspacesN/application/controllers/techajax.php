<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class techajax extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		// session_start();
	}
	/*
	*@ 生成驗證碼圖片
	*/
	public function img()
	{
		include('./include/captcha.php');
		$captcha = new LfsCaptcha(array('width' => 200,'height' => 75));
		/* 
		*@ 圖片如果創建不成功 可能路徑,可能沒有載入 字體 system/fonts/ 
		*@ captcha.php : 89行;
		*/
		$captcha->CreateImage();
	}
	/*
	*@ 檢查驗證碼是否正確
	*/
	function check()
	{
		echo (int)(strtolower($_POST['captcha'])===strtolower($_SESSION['captcha']));
	}
}
