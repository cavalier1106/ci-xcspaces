<?php
class Lfs_notepad extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 
     * 获取用户的笔记
     */
	function get_notepad( $user_info = array() )
	{
		$notepad = $this->db->dbprefix("notepad");
		$users = $this->db->dbprefix("users");

		$order = 'n.time DESC';

		$where = "n.status=1 AND n.create_userid={$user_info['id']}";

		//获取标签ZID对应话题
		$sql = "SELECT n.*, u.name as u_name, u.id as uid, u.img_path as avatar, u.user_avatar
				FROM `{$notepad}` as n
				LEFT JOIN `{$users}` as u ON n.create_userid=u.id
				WHERE {$where}
				ORDER BY {$order}
				LIMIT 10
			";
		$res = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($res);exit;

		return $res;
	}

	/* 
	 * 用户笔记页面总数
	 */
	function member_notepad_count( $uid )
	{
		$notepad = $this->db->dbprefix("notepad");

		$sql = "SELECT * FROM `{$notepad}` WHERE status = 1 AND create_userid = $uid";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}

	/* 
	 * 用户笔记页面分页
	 */
	function memberNotepad_findList( $page, $num, $uid )
	{
		$notepad = $this->db->dbprefix("notepad");
		$users = $this->db->dbprefix("users");

		$order = 'n.time DESC';

		//分页取话题数据
		$offset = ( $page - 1 ) * $num;
		if( $offset == '' )
		{
			$offset = 0;
		}

		$sql = "SELECT n.*, u.img_path as avatar 
				FROM `{$notepad}` as n 
				LEFT JOIN `{$users}` as u ON u.id = n.create_userid
				WHERE n.status=1 AND n.create_userid = $uid
				ORDER BY $order
				LIMIT {$offset},{$num} 
			";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return array('data' => $data);
	}

	//设置笔记本 开放
	function set_notepad_isopen( $post_data, $user_info )
	{	
		$notepad = $this->db->dbprefix("notepad");

		$nId = $post_data['nId'];

		$msg = array( 'code' => 0, 'msg' => '数据保存成功,等待审核' );

		/* xcspaces空间 有效性检查*/
		$z_sql = "SELECT nId,is_open FROM `{$notepad}` WHERE nId = {$nId} AND create_userid = {$user_info['id']} LIMIT 1";
		$res = $this->db->query($z_sql)->row_array();
		if( !$res )
		{
			return array( 'code' => 1, 'msg' => '数据不存在', 'is_open' => 0 );
		}

		if( $res['is_open'] == 1 )
		{
			$is_open = 2;
		}
		else
		{
			$is_open = 1;
		}

		$this->db->where( array('create_userid' => $user_info['id'],'nId' => $nId));
		$res = $this->db->update( 'notepad', array( "is_open" => $is_open ) );

		if( $res )
		{
			$msg = array( 'code' => 2, 'msg' => '笔记本开放设置成功！', 'is_open' => $is_open);
		}
		else
		{
			$msg = array( 'code' => 3, 'msg' => '笔记本开放设置失败！', 'is_open' => 0);
		}

		return $msg;
	}

	/*
	 * 用户删除自己的笔记
	 */
	function del_topic( $nid, $user_info )
	{
		$this->db->where( array('create_userid' => $user_info['id'],'nId' => $nid));
		$res1 = $this->db->update( 'topic', array( "status" => 4) );

		if( $res1 )
		{
			return true;	
		}
		else
		{
			return false;
		}
	}

	/*
	 * 笔记保存
	 */
	function set_notepad( $post_data, $user_info = array() )
	{	
		$notepad = $this->db->dbprefix("notepad");
		$users_expand = $this->db->dbprefix("users_expand");
		
		$msg = array( 'code' => 0, 'msg' => '数据保存成功,等待审核');

		/*
		 * 过滤 <p><br></p>
		 */
		$content = str_replace('<p><br></p>', '', $post_data['content']);

		//内部号不需要过滤
		$IN_USER = config_item('IN_USER');
		$status = 3;
		/*if(in_array($user_info['name'], $IN_USER))
		{
			$status = 1;
		}*/
		if($user_info['user_type'] == 2) $status = 1;

		//话题创建
		$data = array(
				'hashId' => md5( 'www.xcspaces.com' . $user_info['name'] . time() ),
				'title' => $post_data['title'],
				'content' => $content,
				'status' => $status,
				'is_open' => 1,
				'create_userid' => $user_info['id'],
				'create_username' => $user_info['name'],
				'keyworks' => $post_data['title'],
				'description' => $post_data['title'],
				'time' => time(),
			);
		//判断当前用户发布的话题是否已发布过
		$sql = "SELECT nId FROM `{$notepad}` WHERE create_userid = {$user_info['id']} AND title = \"{$post_data['title']}\" LIMIT 1";
		$res = $this->db->query($sql)->row_array();
		if( $res )
		{
			return array( 'code' => 1, 'msg' => '当前笔记用户已经发布过！');
		}

		if( !$this->db->insert('notepad', $data) )
		{
			$msg = array( 'code' => 2, 'msg' => '数据保存异常');
		}
		else
		{
			//发表笔记加+5威望
			//-------------------- 事务处理 -------------------------------
			$sql = "UPDATE `{$users_expand}` SET coin=coin+5 WHERE uid={$user_info['id']}";
			$ret = $this->db->query($sql);
			
			if( $ret )
			{
				//hash分表处理
				$uid = (int)$user_info['id'];
				
				$uid_hash = get_hash( $uid );
				$table = 'coin_record_' . $uid_hash;
				create_coin_record_table( $uid_hash );
				
				//操作类型:
				//0+初始资本,1+每日登录奖励,2+每日活跃度奖励,3+创建主题,4+创建回复,5+主题回复收益,6+注册推荐收益,7-发送谢意,8+创建笔记
				$record_data = array(
						'uid' => $user_info['id'],
						'coin' => +5,
						'desc' => "创建笔记",
						'op_type' => 8,
						'random' => random_string(),
						'time' => time(),
					);
				$recrd = $this->db->insert($table, $record_data);
			}

			//-------------------- 事务处理 END ------------------------------
		}

		return $msg;
	}

	/* 
	 * 根据 笔记 nId 获取相应话题信息 
	 */
	function get_one_notepad( $nId = 0 )
	{
		if( $nId == 0 )
		{
			return array();
		}

		$notepad = $this->db->dbprefix("notepad");
		$users = $this->db->dbprefix("users");

		//查找对应xcspaces 空间的话题

		$sql = "SELECT n.*, u.name as u_name, u.id as u_id, u.img_path as u_avatar
				FROM `{$notepad}` as n
				LEFT JOIN `{$users}` u ON u.id = n.create_userid
				WHERE n.status=1 AND n.nId={$nId}
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

}

?>
