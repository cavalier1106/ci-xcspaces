<?php
class Lfs_topic_reply extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

	/* 
	 * 根据话题id获取相应话题回复信息 
	 * 数据量大了要考虑分表处理
	 */
	function get_topic_reply( $tid=0 )
	{
		if( $tid == 0 )
		{
			return array();
		}

		//hash分表处理
		$tid = (int)$tid;
		
		$hash = get_hash( $tid );
		$table = 'topic_reply_' . $hash;
		//不存在表就创建数据表
		if( !$this->db->table_exists( $table ) )
		{
			$res = create_topic_reply_table( $hash );
		}

		$topic_reply = $this->db->dbprefix( $table );
		$users = $this->db->dbprefix("users");
		$users_expand = $this->db->dbprefix("users_expand");

		//查找对应空间的话题

		$sql = "SELECT t.*, u.name as u_name, u.id as u_id, u.img_path as u_avatar, e.coin
				FROM `{$topic_reply}` as t
				LEFT JOIN `{$users}` u ON u.id = t.reply_userid
				LEFT JOIN `{$users_expand}` e ON e.uid = t.reply_userid
				WHERE t.status=1 AND t.tid={$tid}
			";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 保存话题回复数据 
	 */
	function set_topic_reply( $post_data = array() )
	{
		if( empty( $post_data ) )
		{
			return array();
		}

		//hash分表处理
		$hashId = $post_data['hashId'];
		$tid = topic_hashId_to_id( $hashId );
		
		$hash = get_hash( $tid );
		$table = 'topic_reply_' . $hash;
		create_topic_reply_table( $hash );
		
		//不存在表就创建数据表
		if( !$this->db->table_exists( $table ) )
		{
			$res = create_topic_reply_table( $hash );
		}

		$topic_reply = $this->db->dbprefix( $table );
		$topic = $this->db->dbprefix("topic");
		$users = $this->db->dbprefix("users");
		$users_expand = $this->db->dbprefix("users_expand");

		// ------------------------------------------------------
		//判断相同内容是否评论过，评论过则不在保存记录
		//保存记录关键字处理
		//保存记录成功不能及时显示数据或只有用户自己可以查看
		//-------------------------------------------------------

		$z_sql = "SELECT id FROM `{$users}` WHERE name = '{$post_data['uname']}' LIMIT 1";
		$uid = $this->db->query($z_sql)->row_array();

		$z_sql = "SELECT id FROM `{$topic_reply}` WHERE tid = '{$tid}' AND content = '{$post_data['content']}' LIMIT 1";
		$is_exist = $this->db->query($z_sql)->row_array();

		if( empty( $is_exist ) )
		{
			/*
			 * 过滤 <p><br></p>
			 */
			$content = str_replace('<p><br></p>', '', $post_data['content']);
			$uname = $post_data['uname'];

			//内部号不需要过滤
			$IN_USER = config_item('IN_USER');
			$status = 3;
			if(in_array($uname, $IN_USER))
			{
				$status = 1;
			}
			
			$data = array(
					'tid' => (int)$tid,
					'content' => $content,
					'status' => $status,
					'view_status' => 0,
					'reply_userid' => $uid['id'],
					'reply_username' => $post_data['uname'],
					'time' => time(),
				);
			
			$res = $this->db->insert($topic_reply, $data);

			if( $res )
			{
				/* 
				 * xcspace_coin_record 这个表记录每一次操作 ,相应的用户数据要在表xcspace_users_expand进行加减
				 */
				$z_sql = "SELECT title, create_userid, create_username FROM `{$topic}` WHERE id = {$tid} LIMIT 1";
				$tpc = $this->db->query($z_sql)->row_array();
				
				//-------------------- 事务处理 -------------------------------
				// coin 是否大于 0
				$sql = "UPDATE `{$users_expand}` SET coin=coin-5 WHERE uid={$uid['id']}";
				$ret = $this->db->query($sql);

				$sql = "UPDATE `{$users_expand}` SET coin=coin+5 WHERE uid={$tpc['create_userid']}";
				$res = $this->db->query($sql);
				
				if( $ret && $res )
				{
					//hash分表处理
					// $uid = (int)$uid['id'];
					
					$uid_hash = get_hash( (int)$uid['id'] );
					$table = 'coin_record_' . $uid_hash;
					create_coin_record_table( $uid_hash );

					$record_data = array(
							'uid' => $uid['id'],
							'coin' => -5,
							'desc' => "创建回复",
							'op_type' => 4,
							'random' => random_string(),
							'time' => time(),
						);
					$recrd = $this->db->insert($table, $record_data);

					//hash分表处理
					$create_userid = (int)$tpc['create_userid'];
					
					$create_userid_hash = get_hash( $create_userid );
					$table = 'coin_record_' . $create_userid_hash;
					create_coin_record_table( $create_userid_hash );

					$record_datas = array(
							'uid' => $tpc['create_userid'],
							'coin' => 5,
							'desc' => "话题回复收益",
							'op_type' => 5,
							'random' => random_string(),
							'time' => time(),
						);
					$recrd = $this->db->insert($table, $record_datas);
				}

				//-------------------- 事务处理 END ------------------------------
			}

			// echo "<pre>";print_r( $res );exit;
		}	

		return array( 'code' => 0, 'msg' => '数据保存成功,等待审核');
	}
	
	/* 
	 * 未读提醒
	 */
	function get_notifications_replys( $notifications_replys )
	{
		// $topic = $this->db->dbprefix("topic");
		// $users = $this->db->dbprefix("users");
	}


}

?>
