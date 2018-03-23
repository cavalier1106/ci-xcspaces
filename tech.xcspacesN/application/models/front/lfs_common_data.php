<?php

class Lfs_common_data {

	private $_CI;

	/**
	 * @param string $host
	 * @param int $post
	 */
	public function __construct() 
	{
		$this->_CI = & get_instance();

		if( $this->_CI->is_login )
		{
			//获取用户信息
			$this->_CI->user_info = getUserInfo( $this->_CI->uname );
			//用户头像信息
			$this->_CI->avatar_arr = json_decode( unserialize( $this->_CI->user_info['user_avatar'] ), true );
			//提醒系统
			$this->_CI->notifications = get_notifications( $this->_CI->uname );
			//每日登录奖励是否获取
			$this->_CI->dailyCoinIsGet = dailyCoinIsGet( $this->_CI->uname );
		}

	}
	
}