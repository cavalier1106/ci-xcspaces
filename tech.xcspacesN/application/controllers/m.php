<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M extends CI_Controller {
	
	public $data=array();
	private $default_tpl = 'xcspace/index';
	private $logined = false;

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('parser');
		$this->model();
		$this->library();
		$this->data['hover'] = 'left';
		$this->data['base_url'] = $this->config->item('base_url');
		
		// var_dump($this->session->all_userdata());exit;
	}
	
	function model()
	{
		$this->load->model('tn_servers','servers');
		$this->load->model('tn_order',"order");
		$this->load->model('tn_massagedivision',"massagedivision");
		$this->load->model('tn_user',"user");
	}
	function library()
	{
		// $this->load->library('gettreeid');
	}
	
	/* 首页 */
	public function index()
	{
		$this->data['content'] = 'index';
		$servers_list = $this->servers->get_servers_list();
		$this->data['servers_list'] = $servers_list;
		$this->load->view($this->default_tpl,$this->data);
	}

	/* 子空间 */
	public function go( $node="share" )
	{
		$this->data['content'] = 'go';
		$this->load->view($this->default_tpl,$this->data);
	}
	
	/* 详细主题页面 */
	public function detail($tid=0)
	{
		$this->data['content'] = 'detail';
		$this->load->view($this->default_tpl,$this->data);
	}
	
	/*@ 
	 * 推拿服务详细页面 
	 * type=0 表示：交易成功订单
	 * type=1 表示：未付款订单
	 * type=2 表示：取消订单
	 */
	public function orders($type=0)
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'orders';
		$this->data['hover'] = 'center';
		$type = intval($type);
		$this->data['type'] = $type;
		/* 订单信息 */
		$orders_list = $this->order->get_orders_list($type);
		$this->data['orders_list'] = $orders_list;
		$this->data['isNull'] = count($orders_list) > 0 ? true : false;
		// var_dump($orders_list);exit;
		$this->load->view('index',$this->data);
	}
	
	/* 推拿服务单品详细页面 */
	public function ordersigle($orderNumber='150531100947E5D')
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'ordersigle';
		$orders = $this->order->get_orders($orderNumber);
		$this->data['orders'] = $orders;
		// var_dump($orders);exit;
		$this->load->view('index',$this->data);
	}
	
	/* 支付页面 */
	public function payorder($orderNumber="150531100947E5D")
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'payorder';
		$orders = $this->order->get_buy_orders($orderNumber);
		$this->data['orders'] = $orders;
		
		$this->load->view('index',$this->data);
	}
	
	/* 用户详细页面 */
	public function users()
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		/* get 用户手机号 */
		$this->data['user_info'] = $this->user->is_login(1);
		
		$this->data['content'] = 'users_center';
		$this->data['hover'] = 'right';
		$this->load->view('index',$this->data);
	}
	
	/* 用户余额页面 */
	public function account(){
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'account';
		$this->data['hover'] = 'right';
		$this->load->view('index',$this->data);
	}
	
	/* 用户余额充值页面 */
	public function charge(){
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'charge';
		$this->load->view('index',$this->data);
	}
	
	/* 用户登陆页面 */
	public function login()
	{
		$this->data['content'] = 'login';
		$this->load->view('index',$this->data);
	}
	
	/* 用户注册页面 */
	public function register()
	{
		$this->data['content'] = 'register';
		$this->load->view('index',$this->data);
	}
	
	/*@ 
	 * 用户优惠卷页面 
	 * type=0 表：未使用, type=1 表：已使用
	 */
	public function coupons($type=0)
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'coupons';
		$type = intval($type);
		$this->data['type'] = $type==0 ? 0 : 1;
		/* 获取优惠卷数据 */
		$this->data['coupons_data'] = "";
		$this->load->view('index',$this->data);
	}
	
	/* 公司加入页面 */
	public function joinnode()
	{	
		$this->data['content'] = 'joinnode';
		$this->data['hover'] = 'company';
		$this->load->view('index',$this->data);
	}
	
	/* 购买服务界面 */
	public function buy()
	{
		/* 是否登陆 */
		if(!$this->user->is_login()){
			header("Location:" . $this->data['base_url'] . "m/login");
		}
		
		$this->data['content'] = 'buy';
		//购买服务数量
		$quantity = $this->input->post('quantity');
		$sid = $this->input->post('sid');
		$this->data['quantity'] = intval($quantity);
		//购买服务ID
		$this->data['sid'] = intval($sid);
		
		/* 选择时间 */
		$data = array();
		for($i=0;$i<5;$i++){
			$data[] = date("m-d",strtotime("+{$i} day"));
		}
		$this->data['data'] = $data;
		$this->data['today'] = date("m-d");
		/* 初始化今天时间 */
		$this->data['workTime'] = $this->order->get_workTime(date("Y-m-d"));
		//推拿师
		$massageDivisions = $this->massagedivision->get_massagedivision();
		$this->data['massageDivisions'] = $massageDivisions;
		// var_dump($massageDivisions);exit;
		$this->load->view('index',$this->data);
	}
	
	/* 点滴公益活动页面 */
	public function expnode()
	{
		$this->data['content'] = 'expnode';
		$this->load->view('content/expnode',$this->data);
	}
	
	/* 理疗师详情页面 
	 * m/tuinashi/$sid
 	 */
	public function tuinashi($sid=0)
	{
		$this->data['content'] = 'tuinashi_detail';
		$this->data['sid'] = $sid;
		$sid = intval($sid);
		//指定$sid推拿师
		$massageDivision = $this->massagedivision->get_massagedivision_one($sid);
		$this->data['massageDivision'] = $massageDivision;
		/* 理疗师评价 */
		$reviews = $this->massagedivision->get_service_reviews($sid);
		$this->data['reviews'] = $reviews;
		
		$this->load->view('index',$this->data);
	}
	
	/* 获取更多理疗师详情页面 
	 * m/tuinashi/$sid
 	 */
	function ajax_reviews(){
		$sid = $this->input->post("sid");
		$page = $this->input->post("page");
		$num = $this->input->post("num");
		$reviews = $this->massagedivision->get_service_reviews($sid,$page,$num);
		
		echo json_encode(array());
	}
	
	/* 点滴公益活动页面 */
	public function worktables()
	{
		$this->data['content'] = 'worktables';
		$this->load->view('content/worktables',$this->data);
	}
	
	/* 点滴公益活动页面 */
	public function freeorder($id=1)
	{
		$this->data['content'] = 'freeorder';
		$this->load->view('content/freeorder',$this->data);
	}
	
	/*@
	 * ************************* AJAX *******************************
	 */
	
	//用户登录
	public function ajax_user_login(){
		
		$phone = $this->input->post("phone");
		$pwd = $this->input->post("pwd");
		
		$res = $this->user->login($phone,$pwd);
		
		echo json_encode( $res );
		exit();
	}
	
	/* 存在获取 code */
	public function ajax_isexit_user_login(){
	
		$phone = $this->input->post("phone");
		$res = $this->user->isexit_user_login($phone);
		
		echo json_encode($res);
		exit();
	}
	
	public function ajax_WorkTime(){
		/* 还要判断是否登陆 */
		$date = $this->input->post('date');
		$date_arr = explode('-',$date);
		$month = intval($date_arr[0]);
		$day = intval($date_arr[1]);
		$flag = false;
		/* 过滤时间格式 */
		if(!is_numeric($month) && !is_numeric($day)){
			$res = array(
				"status" => 1,
				"msg" => "非法请求",
			);
			$flag = true;
		}
		if($month < 1 && $month > 12){
			$res = array(
				"status" => 2,
				"msg" => "非法请求",
			);
			$flag = true;
		}
		if($day < 1 && $day > 31){
			$res = array(
				"status" => 3,
				"msg" => "非法请求",
			);
			$flag = true;
		}
		
		if($flag){
			echo json_encode($res);exit;
		}
		
		$year = date('Y');
		$date = date('Y')."-".$date;
		// $start_time = strtotime($year."-".$date." 00:00:00");
		// $end_time = strtotime($year."-".$date." 23:59:59");
		
		/* 返回时间字符串 */
		$get_workTime = $this->order->get_workTime($date);
		
		echo $get_workTime;
		exit();
	}
	
	/* 生成订单 
	 * m/ajax_create_order
 	 */
	function ajax_create_order(){
	
		/* 登陆判断 */
		$quantity = intval($this->input->post("quantity"));
		$sid = intval($this->input->post("sid"));
		$name = $this->input->post("name");
		$mobile = $this->input->post("mobile");
		$address = $this->input->post("address");
		$date = $this->input->post("date");
		$time = $this->input->post("time");
		$tn_id = $this->input->post("tn_id");
		$timestramp = strtotime(date('Y').'-'.trim($date).' '.trim($time).':00');
		// echo $timestramp;exit;
		/* one推拿服务rmb */
		$server_one = $this->servers->get_servers_one($sid);
		$rmb = $server_one['rmb'] * $quantity;
		/* 订单流水号生成算法 */
		$serial_number = "";
		$randomkeys = strtoupper(randomkeys(25));
		$data = array(
			'uid' => 1,
			'uname' => $mobile,
			'phone' => $mobile,
			'time' => $timestramp,
			'order_nums' => $quantity,
			'order_rmb' => $rmb,
			'tn_id' => $tn_id,
			'sid' => $sid,
			'serial_number' => $randomkeys,
			'address' => $address,
			'date' => time()
		);
		$order = $this->order->create_order($data);
		
		echo json_encode($order);
		exit();
	}
	
	/* 用户是否存在
	 * m/ajax_isexit_user
 	 */
	function ajax_isexit_user(){
	
		$mobile = $this->input->post("mobile");
		$res = $this->user->isexit_user($mobile);
		
		echo json_encode($res);
		exit();
	}
	
	/* 创建用户
	 * m/ajax_create_user
 	 */
	function ajax_create_user(){
	
		$mobile = $this->input->post("mobile");
		$pwd = $this->input->post("pwd");
		$pwd_str = randomkeys();
		
		$pwd = md5( md5( trim($pwd) . $pwd_str ) );
		
		$data = array(
			'uname' => $mobile,
			'phone' => $mobile,
			'pwd' => $pwd,
			'code' => $pwd_str,
			'registration_time' => time()
		);
		
		$res = $this->user->create_user($data);
		
		echo json_encode($res);
		exit();
	}
	
	
	
}
