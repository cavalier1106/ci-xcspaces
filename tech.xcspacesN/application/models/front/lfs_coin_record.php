<?php
class Lfs_coin_record extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 保存用户信息 */
	function get_daily_coin( $uid )
	{
		//hash分表处理
		$uid = (int)$uid;
		$uid_hash = get_hash( $uid );
		$table = 'coin_record_' . $uid_hash;
		create_coin_record_table($uid_hash);

		$coin_record = $this->db->dbprefix( $table );
		$users_expand = $this->db->dbprefix("users_expand");
		
		//操作类型:0+初始资本,1+每日登录奖励,2+每日活跃度奖励,3-创建主题,4-创建回复,5+主题回复收益,6+注册推荐收益,7-发送谢意
		//每日登录奖励+10

		$time = strtotime( date('Y-m-d') );
		
		//领取前先判断今天是否已领取？
		$sql = "SELECT uid FROM `{$coin_record}` WHERE uid={$uid} AND time >= $time AND op_type=1 LIMIT 1";

		$msg = array();
		if( !$this->db->query($sql)->row_array() )
		{
			//领取 ---------------- 要加事务------
			$data = array(
					'uid' => $uid,
					'coin' => 5,
					'desc' => '每日登录奖励',
					'op_type' => 1,
					'random' => random_string(),
					'time' => time(),
				);
			$res = $this->db->insert($table, $data);

			//用户信息扩展表
			$sql = "UPDATE `{$users_expand}` SET coin=coin+5 WHERE uid={$uid}";
			$ret = $this->db->query($sql);

			if( $res && $ret )
			{
				
				$msg['code'] = 0;
				$msg['msg'] = '已成功领取每日登录奖励';
			}
			else
			{
				$msg['code'] = 1;
				$msg['msg'] = '失败领取每日登录奖励';
			}
			
		} 
		else
		{
			$msg['code'] = 2;
			$msg['msg'] = '每日登录奖励已领取';
		}

		// echo "<pre>";print_r($msg);exit;

		return $msg;
	}

	/* 领取前先判断今天是否已领取 */
	function is_get_daily_coin( $uid )
	{
		//hash分表处理
		$uid = (int)$uid;
		$uid_hash = get_hash( $uid );
		$table = 'coin_record_' . $uid_hash;
		create_coin_record_table($uid_hash);

		$coin_record = $this->db->dbprefix( $table );
		$users_expand = $this->db->dbprefix("users_expand");
		
		//操作类型:0+初始资本,1+每日登录奖励,2+每日活跃度奖励,3-创建主题,4-创建回复,5+主题回复收益,6+注册推荐收益,7-发送谢意
		//每日登录奖励+10

		$time = strtotime( date('Y-m-d') );
		
		//领取前先判断今天是否已领取？
		$sql = "SELECT uid FROM `{$coin_record}` WHERE uid={$uid} AND time >= $time AND op_type=1 LIMIT 1";

		if( !$this->db->query($sql)->row_array() )
		{
			//今天没有领取
			return true;
		} 
		else
		{
			//今天已领取
			return false;
		}

	}

	/* 
	 *  奖励记录总数
	 */
	function get_coin_record_count( $uid )
	{
		//hash分表处理
		$uid = (int)$uid;
		$uid_hash = get_hash( $uid );
		$table = 'coin_record_' . $uid_hash;
		create_coin_record_table($uid_hash);

		$coin_record = $this->db->dbprefix( $table );

		$sql = "SELECT count(uid) as nums 
				FROM `{$coin_record}` 
				WHERE uid={$uid} 
			";

		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data['nums'];
	}

	/* 
	 * 用户奖励记录列表
	 */
	function get_coin_records_findList( $uid, $page, $num )
	{
		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		//hash分表处理
		$uid = (int)$uid;
		$uid_hash = get_hash( $uid );
		$table = 'coin_record_' . $uid_hash;
		create_coin_record_table($uid_hash);

		$coin_record = $this->db->dbprefix( $table );

		$sql = "SELECT * 
				FROM `{$coin_record}` 
				WHERE uid={$uid}
				ORDER BY time DESC
				LIMIT {$offset},{$num}
			";

		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return array( 'data' => $data );

	}
	
}

?>
