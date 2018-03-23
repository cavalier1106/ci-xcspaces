<?php
class Lfs_user extends CI_Model {
	
	private $_tb;
	private $_pre_tb;

    function __construct()
    {
        parent::__construct();
		$this->load->database();

		$this->_tb = 'users';
        $this->_pre_tb = $this->db->dbprefix("users");
    }

    /* 微博是否有绑定用户账号 */
	function WeiboIsBindUser( $wb_id )
	{
		$sql = "SELECT name FROM `{$this->_pre_tb}` WHERE status='0' AND wb_id='{$wb_id}' AND wbBind='1' LIMIT 1";
		$data = $this->db->query($sql)->row_array();

		return $data;
	}

	/* QQ是否有绑定用户账号 */
	function qqBindUser( $qq_openid )
	{
		$sql = "SELECT name FROM `{$this->_pre_tb}` WHERE status='0' AND qqOpenid='{$qq_openid}' AND qqBind='1' LIMIT 1";
		$data = $this->db->query($sql)->row_array();

		return $data;
	}

    /* 保存用户信息 */
	function set_userInfo( $uname, $post_data )
	{
		$users = $this->db->dbprefix("users");

		$data = array(
				'nickname' => $post_data['nickname'],
				'webname' => $post_data['webname'],
				'email' => $post_data['email'],
				'phone' => $post_data['phone'],
				'qq' => $post_data['qq'],
				// 'sex' => $post_data['sex'],
				'city' => $post_data['city'],
				'address' => $post_data['address'],
				'career' => $post_data['career'],
				'company' => $post_data['company'],
				'website' => $post_data['website'],
				'person_bio' => $post_data['person_bio'],
			);

		$this->db->where('name', $uname);
		$res = $this->db->update('users', $data);

		// echo "<pre>";print_r($post_data);exit;

		return $res;
	}

	/* 获取用户信息 */
	function get_userInfo( $uname )
	{
		$users = $this->db->dbprefix("users");

		$sql = "SELECT * FROM `{$users}` WHERE status=0 AND name='{$uname}' LIMIT 1";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 用户中心数据
	 */
	function get_user_center_info( $uid )
	{
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");
		// $topic_reply = $this->db->dbprefix("topic_reply");
		$get_topic_reply = get_topic_reply("status=1 AND reply_userid={$uid}");

		$sql = "SELECT t.*, n.name, n.tab_name 
				FROM `{$topic}` t 
				LEFT JOIN `{$node_tree}` n ON n.zid = t.zid
				WHERE t.status=1 AND t.create_userid={$uid} 
				ORDER BY t.time DESC
				LIMIT 5
			";
		$topics = $this->db->query($sql)->result_array();

		$sql = "SELECT r.*, t.create_username, t.title
				FROM `{$get_topic_reply}` r 
				LEFT JOIN `{$topic}` t ON t.id = r.tid
				WHERE r.status=1 AND r.reply_userid={$uid} 
				ORDER BY t.time DESC
				LIMIT 5
			";
		$replys = $this->db->query($sql)->result_array();

		$data = array(
				'topics' => $topics,
				'replys' => $replys,
			);

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 用户话题页面总数
	 */
	function member_topics_count( $uid )
	{
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		$sql = "SELECT count(t.id) as nums 
				FROM `{$topic}` t 
				LEFT JOIN `{$node_tree}` n ON n.zid = t.zid
				WHERE t.status=1 AND t.create_userid={$uid} {$this->_fid} 
			";

		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data['nums'];
	}

	/* 
	 * 用户话题页面分页
	 */
	function memberTopics_findList( $page, $num, $uid )
	{
		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		$sql = "SELECT t.*, n.name, n.tab_name 
				FROM `{$topic}` t 
				LEFT JOIN `{$node_tree}` n ON n.zid = t.zid
				WHERE t.status=1 AND t.create_userid={$uid} {$this->_fid}
				ORDER BY t.time DESC
				LIMIT {$offset},{$num}
			";

		$data = $this->db->query($sql)->result_array();

		foreach ($data as &$v) {
			$tid_hash = get_hash( $v['id'] );
			$treply = $this->db->dbprefix('topic_reply_' . $tid_hash);

			//判断表是否存在
			if( !$this->db->table_exists( $treply ) )
			{
				$v['reply_counts'] = 0;
			}
			else
			{
				//用户评论数
				$sql = "SELECT count(*) as reply_counts FROM `{$treply}` WHERE tid='{$v['id']}' AND status=1";
				$rt = $this->db->query($sql)->row_array();
				$v['reply_counts'] = $rt['reply_counts'];
			}
		}

		// echo "<pre>";print_r($data);exit;

		return array( 'data' => $data );
	}

	/* 
	 * 用户话题回复页面分页总数
	 */
	function member_replys_count( $uid ){

		$topic = $this->db->dbprefix("topic");
		// $topic_reply = $this->db->dbprefix("topic_reply");
		$get_topic_reply = get_topic_reply("status=1 AND reply_userid={$uid}");
		
		if( !$get_topic_reply )
		{
			return 0;
		}

		$sql = "SELECT count(r.id) as nums
				FROM `{$get_topic_reply}` r 
				LEFT JOIN `{$topic}` t ON t.id = r.tid
				WHERE r.status=1 AND r.reply_userid={$uid} {$this->_fid}
			";

		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data['nums'];
	}

	/* 
	 * 用户话题回复页面分页
	 */
	function memberReplys_findList( $page, $num, $uid ){

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		$topic = $this->db->dbprefix("topic");
		// $topic_reply = $this->db->dbprefix("topic_reply");
		$get_topic_reply = get_topic_reply("status=1 AND reply_userid={$uid}");

		if( !$get_topic_reply )
		{
			return array( 'data' => array() );
		}

		$sql = "SELECT r.*, t.create_username, t.create_userid, t.title, t.hashId
				FROM `{$get_topic_reply}` r 
				LEFT JOIN `{$topic}` t ON t.id = r.tid
				WHERE r.status=1 AND r.reply_userid={$uid} {$this->_fid}
				ORDER BY t.time DESC
				LIMIT {$offset},{$num}
			";

		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return array( 'data' => $data );
	}

	/* 保存用户头像信息 */
	function set_userAvatar( $uname, $uid, $avatar_str )
	{
		$data = array(
				'img_path' => 'avatar/' . $uid . '/' . $uid . '_large.png',
				'user_avatar' => $avatar_str,
			);

		$where = "id = {$uid} AND name = '{$uname}'";

		//所有的值自动被转义，生成安全的查询语句
		$str = $this->db->update_string('users', $data, $where);

		$res = $this->db->query($str);

		// echo "<pre>";print_r($res);exit;

		return $res;
	}

	/* 
	 * 我关注用户总数
	 */
	function focus_member_count( $uid = 0 )
	{
		if( $uid == 0 )
		{
			return 0;
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$users = $this->db->dbprefix("users");

		//查找对应空间的话题
		$sql = "SELECT users_str
				FROM `{$topic_user}`
				WHERE uid={$uid}
			";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( empty( $res ) )
		{
			return 0;
		}

		if( !$res['users_str'] )
		{
			return 0;
		}

		$sql = "SELECT * FROM `{$users}` WHERE status=0 AND id IN({$res['users_str']})";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}

	/* 
	 * 我关注用户列表
	 * COMMENT '用户账号锁定状态:0:正常,1封号,2禁言,3封号且禁言'
	 */
	function focus_member( $uid = 0, $page = 1, $setting )
	{
		if( $uid == 0 )
		{
			return array();
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$users = $this->db->dbprefix("users");

		//查找对应空间的话题

		$sql = "SELECT users_str FROM `{$topic_user}` WHERE uid={$uid}";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( !$res )
		{
			return array('data' => array());
		}
		if( !$res['users_str'] )
		{
			return array('data' => array());
		}

		//分页取话题数据
		$num = $setting['per_page'];
		$offset = ( $page - 1 ) * $num;
		if( $offset == '' )
		{
			$offset = 0;
		}

		$sql = "SELECT id,name,img_path,user_avatar,reg_time
				FROM `{$users}` 
				WHERE status=0 AND id IN({$res['users_str']})
				ORDER BY reg_time DESC
				LIMIT {$offset},{$num} 
			";
		$data = $this->db->query($sql)->result_array();
		
		// echo "<pre>";print_r($data);exit;

		return array('data' => $data);
	}

	/**
	 * 修改用户密码 
	 */
	public function setpasswd( $post_data = array(), $uname )
	{
		if( empty( $post_data ) ) return array( 'code'=> 1, 'msg'=> 'post参数错误！');

		$password = $post_data['password'];
		$newPassword = $post_data['newPassword'];
		$confirmPassword = $post_data['confirmPassword'];
		if( empty( $password ) || empty( $newPassword ) || empty( $confirmPassword ) )
		{
			return array( 'code'=> 2, 'msg'=> '参数错误！');
		}

		if( $newPassword != $confirmPassword )
		{
			return array( 'code'=> 3, 'msg'=> '两次输入新密码不一致！');
		}

		/*
		 * 查看是否存在这条记录
		 */
		$users = $this->db->dbprefix("users");

		//查找对应空间的话题

		$sql = "SELECT sign FROM `{$users}` WHERE name='{$uname}'";
		$res = $this->db->query($sql)->row_array();
		if( $res )
		{
			$pwd = md5( $newPassword . $res['sign'] );
			$this->db->where('name', $uname);
			if( $this->db->update('users', array('passwd' => $pwd)) )
			{
				return array( 'code'=> 0, 'msg'=> '用户密码修改成功！');
			}
			else
			{
				return array( 'code'=> 4, 'msg'=> '用户密码修改失败！');
			}
		}
		else
		{
			return array( 'code'=> 5, 'msg'=> '用户登录异常！');
		}

	}
	
}

?>
