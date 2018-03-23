<?php
/**
 * @description: redis操作类
 * @file: lib_session.php
 * @author: Quan Zelin
 * @charset: UTF-8
 * @time: 2014-12-11  16:30
 * @version 1.0
 **/

class Lib_session {

	private $_CI;
	private $redis_conf;
	private $_tab_name;
	private $_fid;

	/**
	 * @param string $host
	 * @param int $post
	 */
	public function __construct() 
	{
		$this->_CI = & get_instance();
		$this->redis_conf = $this->_CI->config->item('redis');
		$this->_CI->load->library('lib_redis', $this->redis_conf['xcspace2']);

		$this->ini_session();

		// 特定技术－－写死
		$this->_CI->_tab_name = "AND t.tab_name='tech'";
		$this->_CI->_fid = 'AND t.fid=1';

		if( isset( $_SESSION['user'] ) )
		{
			$this->_CI->uname = strtolower( $_SESSION['user']['uname'] );
		}
		else
		{
			$this->_CI->uname = '';
		}

		$this->_CI->is_login = false;

		// 在线人数
		$this->_CI->user_online_counts = count( $this->_CI->redis->keys('*XCSPACES_USER*') );
		//最高记录
		$this->_CI->user_online_counts = $this->_CI->user_online_counts.rand(0,9).rand(0,9); 
		$user_online_max_counts = intval($this->_CI->redis->get('user_online_max_counts'));
		$this->_CI->user_online_max_counts = $this->_CI->user_online_counts > $user_online_max_counts ? $this->_CI->user_online_counts : $user_online_max_counts;
		$this->_CI->redis->set('user_online_max_counts', $this->_CI->user_online_max_counts);

		if( unserialize( $this->_CI->redis->get("XCSPACES_USER[{$this->_CI->uname}]") ) )
		{
			$this->_CI->is_login = true;
		}

		/* 
		 * ci框架如何手动进行csrf攻击防范 
		 */
		$this->_CI->token_name = $this->_CI->security->get_csrf_token_name();
		$this->_CI->token_hash = $this->_CI->security->get_csrf_hash();

		$this->filter_ip();
		
	}
	
	public function ini_session()
	{
		// session_set_cookie_params( 0, '/', 'test.xcspace.cn' );
		session_set_cookie_params( 0, '/', '' );
	    session_save_path( 'session' );
	    session_cache_expire( 30 * 24 * 3600 );
	    session_name( 'PHPSESSID' );

    	//还原 session 场景
		if( isset( $_REQUEST['PHPSESSID'] ) )
		{
			if($_REQUEST['PHPSESSID'] !== "" && $_REQUEST["PHPSESSID"] !== "undefined") 
			{
				@session_id($_REQUEST['PHPSESSID']);
			}
		}

		session_start();
	}
	
	/* 
	 * 1分钟内操作太频繁的(超过60次) 封IP 一天 ,如果经常操作的 禁止IP
	 */
	public function filter_ip()
	{
		$real_ip = real_ip();
		
		/* 
		 * 优先处理ip黑名单，ip在黑名单中 直接返回  
		 * echo real_ip();
		 * echo "<br>";
		 * echo '您的IP禁止访问！';
		 * exit;
		 */
		$ip_sadd = $this->_CI->redis->smembers('filter_only_ip');
		if( in_array($real_ip, $ip_sadd) )
		{
			 echo $real_ip;
			 echo "<br>";
			 echo '您的IP禁止访问！';
			 exit();
		}
		// echo "<pre>";var_dump($ip_sadd);exit;
		/* 
		 * 获取IP保存时间 
		 */
		$v_time = $this->_CI->redis->hget($real_ip, 'time');

		//一分钟内 操作超过60次 封IP
		if( $v_time && $v_time + 60 > time() )
		{
			$viewTimes = (int)$this->_CI->redis->hget($real_ip, 'viewTimes');
			// 访问次数小于 60 次
			if( $viewTimes < 60 )
			{
				$this->_CI->redis->hset($real_ip, 'viewTimes', $viewTimes + 1);
			}
			else
			{
				//保存IP到数据库
				// save_filter_ip( $real_ip, $this->_CI->uname);
				if( !$this->_CI->uname )
				{
					$i_uname = '游客';
				}
				else
				{
					$i_uname = $this->_CI->uname;
				}
				$this->_CI->redis->sadd('filter_ip', $real_ip . ',' . $i_uname . ',' . time());
				$this->_CI->redis->sadd('filter_only_ip', $real_ip);
				//一分钟内 操作超过60次 封IP,一天后 解封IP
				if( $v_time + 60 + 24 * 3600 > time() )
				// if( $v_time + 60 > time() )
				{
					echo $real_ip;
					echo "<br>";
					echo '操作太频繁,请稍后在试！';
					exit;
				}
				else
				{
					$this->_CI->redis->hset($real_ip, 'viewTimes', 0);
					$this->_CI->redis->hset($real_ip, 'time', time());
				}
			}
		}
		else
		{
			$this->_CI->redis->hset($real_ip, 'viewTimes', 0);
			$this->_CI->redis->hset($real_ip, 'time', time());
		}
	}
}