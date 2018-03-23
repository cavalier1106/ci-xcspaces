<?php
class Lfs_titles extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
		$this->zdy_db = $this->load->database('xcs_write', true);
    }

    /* 递归获取xcspaces 空间title */
	function get_node_titles( $tab='home', $t='new' )
	{
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");
		$users = $this->db->dbprefix("users");
		$topic_clicks = $this->db->dbprefix("topic_clicks");

		if( $t == 'new' )
		{
			$order = 't.time DESC';
		}
		else
		{
			$order = 'c.clicks DESC';
		}

		/*
		 * 所有空间 特殊处理
		 */
		if( $tab == 'home' )
		{
			$where = "t.status=1 AND t.fid=1";
		}
		else
		{
			//获取标签ZID
			$sql = "SELECT zid FROM `{$node_tree}` WHERE tab_name='{$tab}' AND status=1 LIMIT 1";
			$res = $this->db->query($sql)->row_array();
			// echo "<pre>";print_r($res);exit;
			$where = "t.fid={$res['zid']} AND t.status=1";
		}

		//获取标签ZID对应话题
		$sql = "SELECT t.*, c.clicks, n.name as n_name, n.tab_name, u.name as u_name, u.id as uid, u.img_path as avatar
				FROM `{$topic}` as t
				LEFT JOIN `{$node_tree}` as n ON t.zid=n.zid
				LEFT JOIN `{$users}` as u ON t.create_userid=u.id
				LEFT JOIN `{$topic_clicks}` as c ON c.tid=t.id
				WHERE {$where}
				ORDER BY {$order}
				LIMIT 20
			";
		$res = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($res);exit;


		foreach ($res as &$v) 
		{
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

		/*$sql = "SELECT t.*, n.name as n_name, n.tab_name, u.name as u_name, u.id as uid, u.img_path as avatar
				FROM `{$topic}` as t
				LEFT JOIN `{$node_tree}` as n ON t.fid=n.zid
				LEFT JOIN `{$users}` as u ON t.create_userid=u.id
				WHERE n.tab_name='{$tab}' AND t.status=1 
				ORDER BY t.time desc
			";
		$res = $this->db->query($sql)->result_array();*/

		// echo "<pre>";print_r($res);exit;

		return $res;
	}

	 /* 今日热议话题显示处理 */
	function get_hot_titles()
	{
		// ------------------------ 日后修改 ----------------------------
		// $time = strtotime( date('Y-m-d') . ' 00:00:00' );
		$time = strtotime( '-90 day' );

		// $topic_reply = $this->db->dbprefix("topic_reply");
		$get_topic_reply = get_topic_reply();

		if( !$get_topic_reply )
		{
			return array();
		}

		$topic = $this->db->dbprefix("topic");
		$users = $this->db->dbprefix("users");

		//统计出今日回复热贴
		$sql = "SELECT tid, count(tid) as topic_reply_counts
				FROM `{$get_topic_reply}`
				WHERE status=1 AND time >= {$time}
				group by tid 
				ORDER BY topic_reply_counts desc
				LIMIT 10
			";
		$topic_data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($topic_data);exit;

		//在根据热贴查找相应的用户信息和最新回复
		$hot_topics = array();
		if( !empty( $topic_data ) )
		{
			foreach ($topic_data as $k => $v) 
			{
				//创建话题的用户信息
				$sql = "SELECT t.title, t.hashId, t.topic_type, u.name as u_name, u.id as uid, u.img_path as avatar
					FROM `{$topic}` as t
					LEFT JOIN {$users} as u ON t.create_userid = u.id
					WHERE t.id = {$v['tid']} AND t.status=1 AND t.fid=1
				";
				$topic_user_info = $this->db->query($sql)->row_array();

				if(!$topic_user_info) continue;
				
				$hot_topics[$k]['title'] = $topic_user_info['title'];
				$hot_topics[$k]['hashId'] = $topic_user_info['hashId'];
				$hot_topics[$k]['topic_type'] = $topic_user_info['topic_type'];
				$hot_topics[$k]['tid'] = $v['tid'];
				$hot_topics[$k]['u_name'] = $topic_user_info['u_name'];
				$hot_topics[$k]['uid'] = $topic_user_info['uid'];
				$hot_topics[$k]['avatar'] = $topic_user_info['avatar'];

				//回复话题的最新一条回复跟回复用户的信息(用户名跟回复时间)
				$sql = "SELECT *
					FROM `{$get_topic_reply}`
					WHERE tid = {$v['tid']} AND status=1
					ORDER BY time desc
					LIMIT 1
				";

				$topic_reply_info = $this->db->query($sql)->row_array();

				// echo "<pre>";print_r($topic_reply_info);exit;

				$hot_topics[$k]['content'] = $topic_reply_info['content'];
				$hot_topics[$k]['reply_userid'] = $topic_reply_info['reply_userid'];
				$hot_topics[$k]['reply_username'] = $topic_reply_info['reply_username'];
				$hot_topics[$k]['time'] = $topic_reply_info['time'];
			}
		}
		
		return $hot_topics;
	}

	/* 
	 * 根据话题id获取相应话题信息 
	 */
	function get_one_topic( $tid=0 )
	{
		if( $tid == 0 )
		{
			return array();
		}

		$this->load->database('xcs_read', true);

		$topic = $this->db->dbprefix("topic");
		$topic_clicks = $this->db->dbprefix("topic_clicks");
		$users = $this->db->dbprefix("users");

		//查找对应xcspaces 空间的话题

		$sql = "SELECT t.*, c.clicks, u.name as u_name, u.id as u_id, u.img_path as u_avatar
				FROM `{$topic}` as t
				LEFT JOIN `{$topic_clicks}` c ON c.tid = t.id
				LEFT JOIN `{$users}` u ON u.id = t.create_userid
				WHERE t.status=1 AND t.id={$tid}
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 对应xcspaces 空间的话题 */
	function topics_total( $znode_name )
	{
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		//查找对应xcspaces 空间的ZID

		$sql = "SELECT zid
				FROM `{$node_tree}`
				WHERE status=1 AND tab_name='{$znode_name}'
				LIMIT 1
			";
		$zid = $this->db->query($sql)->row_array();

		// 不存在话题ID,可能是随便输入数据
		if( empty( $zid ) )
		{
			error_404();
		}

		//查找对应xcspaces 空间的话题

		$sql = "SELECT count(*) as nums
				FROM `{$topic}`
				WHERE status=1 AND zid={$zid['zid']}
			";
		$data = $this->db->query($sql)->row_array();

		$data['zid'] = $zid['zid'];

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	function findlist( $page, $num, $zid, $t ){

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		$topic = $this->db->dbprefix("topic");
		$users = $this->db->dbprefix("users");
		$topic_clicks = $this->db->dbprefix("topic_clicks");

		if( $t == 'new' )
		{
			$order = 't.time DESC';
		}
		else
		{
			$order = 'c.clicks DESC';
		}

		$sql = "SELECT t.*, u.img_path as avatar, c.clicks 
				FROM `{$topic}` as t
				LEFT JOIN `{$users}` as u ON u.id=t.create_userid
				LEFT JOIN `{$topic_clicks}` as c ON c.tid=t.id
				WHERE t.status=1 AND t.zid={$zid}
				ORDER BY {$order}
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

		return $data;
	}

	/* 对应xcspaces 空间的话题 */
	function search_topics_total( $search_content = '' )
	{
		$topic = $this->db->dbprefix("topic");

		if( $search_content == '' )
		{
			return array( 'nums' => 0 );
		}

		$sql = "SELECT count(*) as nums
				FROM `{$topic}`
				WHERE status=1 AND title LIKE '%{$search_content}%'
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	function search_topics_findlist( $page, $num, $search_content = '' ){

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		if( $search_content == '' )
		{
			return array();
		}

		$topic = $this->db->dbprefix("topic");
		
		$sql = "SELECT *
				FROM `{$topic}`
				WHERE status=1 AND title LIKE '%{$search_content}%'
				ORDER BY time
				LIMIT {$offset},{$num}
		";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 我的收藏话题总数
	 */
	function collection_topics_count( $uid = 0 )
	{
		if( $uid == 0 )
		{
			return 0;
		}

		$topic_user = $this->db->dbprefix("topic_user");

		//查找对应xcspaces 空间的话题

		$sql = "SELECT titles_str
				FROM `{$topic_user}`
				WHERE uid={$uid}
			";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( empty( $res ) )
		{
			return 0;
		}

		$data = explode(',', $res['titles_str']);

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}

	/* 
	 * 我的收藏话题列表
	 */
	function collection_topics( $uid = 0, $page = 1, $setting )
	{
		if( $uid == 0 )
		{
			return array();
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$users = $this->db->dbprefix("users");

		//查找对应xcspaces 空间的话题

		$sql = "SELECT titles_str
				FROM `{$topic_user}`
				WHERE uid={$uid}
			";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( !$res )
		{
			return array( 'nums' => 0, 'data' => array() );
		}

		if( !$res['titles_str'] )
		{
			return array( 'nums' => 0, 'data' => array() );
		}

		$tid_arr = explode(',', $res['titles_str']);

		$nums = count( $tid_arr );

		//分页取话题数据
		$num = $setting['per_page'];
		$offset = ( $page - 1 ) * $num;
		if( $offset == '' )
		{
			$offset = 0;
		}

		$tid_arr = array_slice($tid_arr, $offset, $num);

		if( !$tid_arr )
		{
			return array( 'nums' => 0, 'data' => array() );
		}

		$tid_str = implode(',', $tid_arr);

		$topic = $this->db->dbprefix("topic");

		$sql = "SELECT * FROM `{$topic}` WHERE status=1 AND id IN({$tid_str})";
		$data = $this->db->query($sql)->result_array();

		foreach ($data as $k => &$v) 
		{
			$sql = "SELECT img_path as avatar FROM `{$users}` WHERE status=0 AND id = {$v['create_userid']}";
			$ret = $this->db->query($sql)->row_array();
			$v['avatar'] = $ret['avatar'];
		}

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

		// echo "<pre>";print_r($tid_str);exit;

		return array('nums' => $nums, 'data' => $data);
	}

	/* 
	 * 我关注用户话题总数
	 */
	function focus_member_topics_count( $uid = 0 )
	{
		if( $uid == 0 )
		{
			return 0;
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$topic = $this->db->dbprefix("topic");

		//查找对应xcspaces 空间的话题

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

		$sql = "SELECT * FROM `{$topic}` WHERE status=1 AND create_userid IN({$res['users_str']})";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}
	
	/* 
	 * 我关注用户话题列表
	 */
	function focus_member_topics( $uid = 0, $page = 1, $setting )
	{
		if( $uid == 0 )
		{
			return array();
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$topic = $this->db->dbprefix("topic");
		$users = $this->db->dbprefix("users");

		//查找对应xcspaces 空间的话题

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

		$sql = "SELECT * 
				FROM `{$topic}` 
				WHERE status=1 AND create_userid IN({$res['users_str']})
				ORDER BY time DESC
				LIMIT {$offset},{$num} 
			";
		$data = $this->db->query($sql)->result_array();

		foreach ($data as $k => &$v) 
		{
			$sql = "SELECT img_path as avatar FROM `{$users}` WHERE status=0 AND id = {$v['create_userid']}";
			$ret = $this->db->query($sql)->row_array();
			$v['avatar'] = $ret['avatar'];
		}


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

		return array('data' => $data);
	}

	/* 
	 * 更多话题列表总数
	 */
	function more_topics_count()
	{
		$topic = $this->db->dbprefix("topic");

		$sql = "SELECT * FROM `{$topic}` WHERE status=1 AND fid=1";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}
	
	/* 
	 * 更多话题列表
	 */
	function more_topics( $page = 1, $setting, $t = 'new' )
	{
		$topic_user = $this->db->dbprefix("topic_user");
		$topic = $this->db->dbprefix("topic");
		$users = $this->db->dbprefix("users");
		$topic_clicks = $this->db->dbprefix("topic_clicks");
		$node_tree = $this->db->dbprefix("node_tree");

		if( $t == 'new' )
		{
			$order = 't.time DESC';
		}
		else
		{
			$order = 'c.clicks DESC';
		}

		//查找对应xcspaces 空间的话题

		$sql = "SELECT users_str FROM `{$topic_user}`";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( !$res )
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

		$sql = "SELECT t.*, c.clicks, n.name as n_name, n.tab_name, u.img_path as avatar 
				FROM `{$topic}` as t 
				LEFT JOIN `{$node_tree}` as n ON t.zid=n.zid
				LEFT JOIN `{$users}` as u ON u.id = t.create_userid
				LEFT JOIN `{$topic_clicks}` as c ON c.tid=t.id
				WHERE t.status=1 AND t.fid=1
				ORDER BY $order
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

		return array('data' => $data);
	}

	//话题保存
	function set_tpoic( $post_data, $user_info = array() )
	{	
		$node_tree = $this->db->dbprefix("node_tree");
		$users_expand = $this->db->dbprefix("users_expand");
		$topic = $this->db->dbprefix("topic");
		
		$zid = (int)$post_data['zid'];
		//获取子xcspaces 空间信息
		$z_sql = "SELECT fid FROM `{$node_tree}` WHERE zid = {$zid} AND status = 1 LIMIT 1";
		$fid = $this->db->query($z_sql)->row_array();
		
		$msg = array( 'code' => 0, 'msg' => '数据保存成功,等待审核');

		if( $fid )
		{
			$uid = (int)$user_info['id'];
			/*
			 * 过滤 <p><br></p>
			 */
			$content = str_replace('<p><br></p>', '', $post_data['content']);

			//内部号不需要过滤
			$IN_USER = config_item('IN_USER');
			$status = 3;
			// if(in_array($user_info['name'], $IN_USER))
			// {
			// 	$status = 1;
			// }
			if($user_info['user_type'] == 2) $status = 1;
			
			$topic_type = isset($post_data['topic_type']) ? (int)$post_data['topic_type'] : 1;

			$out_link = '';
			if($topic_type==2){
				$out_link = isset($post_data['out_link']) ? $post_data['out_link'] : '';
				//特定 开发者头条网站
				$filterStr = '?hmsr=toutiao.io&utm_medium=toutiao.io&utm_source=toutiao.io';
				$out_link = str_replace($filterStr, '', $out_link);
				//存在？用 & ; 不存在则用 ？； 
				$flag = strpos('?', $out_link) ? '&' : '?';
				$out_link = $out_link ? $out_link.$flag.'xcs_source=xcspace.com' : 'www.xcspaces.com';
				$out_link_new = str_replace(array('http://','https://'), array('',''), $out_link);
				$content = substr($out_link_new, 0, strpos($out_link_new, '/'));
			}
			$out_link = $out_link ? addslashes($out_link) : '';

			$time = time();
			$name = $user_info['name'];
			$title = $post_data['title'];

			//内部号数据特殊处理
			if($user_info['user_type'] == 2)
			{
				$res = file_get_contents('./doc/user_type_2.json');
				$res = json_decode($res, true);
				if($res){
					$count = count($res)-1;
					$rand = rand(0, $count);
					$name = $res[$rand]['name'];
					$uid = $res[$rand]['id'];
				}
			}
			
			//话题创建
			$data = array(
					'hashId' => md5( 'www.xcspaces.com' . $name . $time ),
					'fid' => $fid['fid'],
					'zid' => $post_data['zid'],
					'title' => $title,
					'keyworks' => $title,
					'description' => $title,
					'content' => $content,
					'status' => $status,
					'create_userid' => $uid,
					'create_username' => $name,
					'topic_type' => $topic_type,
					'out_link' => $out_link,
					'time' => $time,
				);
			//判断当前用户发布的话题是否已发布过
			$sql = "SELECT id FROM `{$topic}` WHERE create_userid = {$uid} AND title = \"{$title}\" LIMIT 1";
			$res = $this->db->query($sql)->row_array();
			if( $res )
			{
				return array( 'code' => 1, 'msg' => '当前话题用户已经发布过！');
			}

			if( !$this->db->insert('topic', $data) )
			{
				$msg = array( 'code' => 2, 'msg' => '数据保存异常');
			}
			else
			{
				//发表话题扣10铜币
				//-------------------- 事务处理 -------------------------------
				$sql = "UPDATE `{$users_expand}` SET coin=coin+10 WHERE uid={$uid}";
				$ret = $this->db->query($sql);
				
				if( $ret )
				{
					//hash分表处理
					$uid_hash = get_hash( $uid );
					$table = 'coin_record_' . $uid_hash;
					create_coin_record_table( $uid_hash );
					
					$record_data = array(
							'uid' => $uid,
							'coin' => +10,
							'desc' => "发布话题",
							'op_type' => 3,
							'random' => random_string(),
							'time' => time(),
						);
					$recrd = $this->db->insert($table, $record_data);
				}

				//-------------------- 事务处理 END ------------------------------
			}
		}
		else
		{
			$msg = array( 'code' => 2, 'msg' => 'xcspaces空间 查询异常！');
		}

		return $msg;
	}

	//xcspaces空间页面话题保存
	function topics_save( $post_data, $user_info )
	{	
		$node_tree = $this->db->dbprefix("node_tree");
		$users_expand = $this->db->dbprefix("users_expand");
		$topic = $this->db->dbprefix("topic");

		$nid = explode(',', $post_data['nid']);
		if( count( $nid ) == 2 )
		{
			$fid = $nid[0];
			$zid = $nid[1];
			/* xcspaces空间 有效性检查*/
			$z_sql = "SELECT zid FROM `{$node_tree}` WHERE zid = {$zid} AND fid = {$fid} LIMIT 1";
			$res = $this->db->query($z_sql)->row_array();
			if( !$res )
			{
				return array( 'code' => 1, 'msg' => '数据不存在');
			}
		}
		else
		{
			return array( 'code' => 2, 'msg' => '数据异常');
		}
		
		$msg = array( 'code' => 0, 'msg' => '数据保存成功,等待审核');

		/*
		 * 过滤 <p><br></p>
		 */
		$content = str_replace('<p><br></p>', '', $post_data['content']);

		//内部号不需要过滤
		$IN_USER = config_item('IN_USER');
		$status = 3;
		if(in_array($user_info['name'], $IN_USER))
		{
			$status = 1;
		}

		//话题保存
		$data = array(
				'hashId' => md5( 'www.xcspaces.com' . $user_info['name'] . time() ),
				'fid' => $fid,
				'zid' => $zid,
				'title' => $post_data['title'],
				'keyworks' => $post_data['title'],
				'description' => $post_data['title'],
				'content' => $content,
				'status' => $status,
				'create_userid' => $user_info['id'],
				'create_username' => $user_info['name'],
				'time' => time(),
			);
		//判断当前用户发布的话题是否已发布过
		$sql = "SELECT id FROM `{$topic}` WHERE create_userid = {$user_info['id']} AND title = \"{$post_data['title']}\" LIMIT 1";
		$res = $this->db->query($sql)->row_array();
		if( $res )
		{
			return array( 'code' => 3, 'msg' => '当前话题用户已经发布过！');
		}

 		if( !$this->db->insert('topic', $data) )
		{
			$msg = array( 'code' => 4, 'msg' => '数据保存异常');
		}
		else
		{
			//发表话题扣1铜币
			//-------------------- 事务处理 -------------------------------
			// coin 是否大于 0
			$sql = "UPDATE `{$users_expand}` SET coin=coin+10 WHERE uid={$user_info['id']}";
			$ret = $this->db->query($sql);
			
			if( $ret )
			{
				//hash分表处理
				$uid = (int)$user_info['id'];
				
				$uid_hash = get_hash( $uid );
				$table = 'coin_record_' . $uid_hash;
				create_coin_record_table( $uid_hash );

				$record_data = array(
						'uid' => $user_info['id'],
						'coin' => +10,
						'desc' => "发布话题",
						'op_type' => 3,
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
	 * 收藏话题,关注用户,关注xcspaces 空间,话题感谢
	 */
	function set_topic_favorite( $node_and_topic_and_user_id, $user_info, $column = 'titles_str', $create_userid = 0 )
	{
		$topic_user = $this->db->dbprefix("topic_user");
		$topic = $this->db->dbprefix("topic");
		$users_expand = $this->db->dbprefix("users_expand");
		
		$z_sql = "SELECT {$column} FROM `{$topic_user}` WHERE uid = {$user_info['id']} LIMIT 1";
		$res = $this->db->query($z_sql)->row_array();
		if( $res )
		{
			$arr = explode(',', $res[$column]);
			$arr[] = $node_and_topic_and_user_id;
			$arr_str = trim( implode(',', $arr), ',');

			//处理完数据在保存
			$this->db->where('uid', $user_info['id']);
			$this->db->update( 'topic_user', array( "$column" => $arr_str) );
		}
		else
		{
			//没有记录就插入一条数据
			//处理完数据在保存
			$titles_str = '';
			$users_str = '';
			$nodes_str = '';
			$thanks_topic_str = '';
			if( $column == 'titles_str' )
			{
				$titles_str = trim($node_and_topic_and_user_id, ',');
			}
			elseif( $column == 'users_str' )
			{
				$users_str = trim($node_and_topic_and_user_id, ',');
			}
			elseif( $column == 'nodes_str' )
			{
				$nodes_str = trim($node_and_topic_and_user_id, ',');
			}
			elseif( $column == 'thanks_topic_str' )
			{
				$thanks_topic_str = trim($node_and_topic_and_user_id, ',');
			}

			$data = array(
					'uid' => $user_info['id'],
					'nodes_str' => $nodes_str,
					'titles_str' => $titles_str,
					'users_str' => $users_str,
					'thanks_topic_str' => $thanks_topic_str,
				);
			$this->db->insert( 'topic_user', $data );
		}

		//感谢用户话题
		if( $column == 'thanks_topic_str' )
		{
			/* 
			 * xcspace_coin_record 这个表记录每一次操作 ,相应的用户数据要在表xcspace_users_expand进行加减
			 */
			$create_userid = (int)$create_userid;
			$z_sql = "SELECT title, create_userid, create_username FROM `{$topic}` WHERE id = {$node_and_topic_and_user_id} AND create_userid = {$create_userid} LIMIT 1";
			$tpc = $this->db->query($z_sql)->row_array();

			// echo "<pre>";print_r($tpc);exit;
			
			//防止用户窜改数据
			if( !$tpc )
			{
				return;
			}
			//-------------------- 事务处理 -------------------------------
			// coin 是否大于 0
			$sql = "UPDATE `{$users_expand}` SET coin=coin-5 WHERE uid={$user_info['id']}";
			$ret = $this->db->query($sql);

			$sql = "UPDATE `{$users_expand}` SET coin=coin+5 WHERE uid={$tpc['create_userid']}";
			$res = $this->db->query($sql);
			
			if( $ret && $res )
			{
				//hash分表处理
				$uid = (int)$user_info['id'];
				
				$uid_hash = get_hash( $uid );
				$table = 'coin_record_' . $uid_hash;
				create_coin_record_table( $uid_hash );

				$record_data = array(
						'uid' => $user_info['id'],
						'coin' => -5,
						'desc' => "感谢 {$tpc['create_username']} 的话题 › {$tpc['title']}",
						'op_type' => 7,
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
						'desc' => "{$user_info['name']} 的感谢",
						'op_type' => 7,
						'random' => random_string(),
						'time' => time(),
					);
				$recrd = $this->db->insert($table, $record_datas);
			}

			//-------------------- 事务处理 END ------------------------------
		}

		return;
	}

	/*
	 * 取消收藏话题,关注用户,关注xcspaces 空间,话题感谢
	 */
	function set_topic_unfavorite( $node_and_topic_and_user_id, $user_info, $column='titles_str' )
	{
		$topic_user = $this->db->dbprefix("topic_user");
		//
		$z_sql = "SELECT {$column} FROM `{$topic_user}` WHERE uid = {$user_info['id']} LIMIT 1";
		$res = $this->db->query($z_sql)->row_array();
		$arr = explode(',', $res[$column]);

		//php按指定元素值去除数组元素的实现方法
		unset( $arr[array_search($node_and_topic_and_user_id, $arr)] );
		$arr_str = trim( implode(',', $arr), ',');

		//处理完数据在保存
		$this->db->where('uid', $user_info['id']);
		$this->db->update( 'topic_user', array( "$column" => $arr_str) );

		return;
	}

	/*
	 * 感谢话题回复
	 */
	function set_thanks_reply( $tid, $user_info, $column = 'thanks_reply_topic_str', $reply_userid = 0, $rid )
	{
		$users_expand = $this->db->dbprefix("users_expand");
		$uid = (int)$user_info['id'];

		//哈希对应表
		$tid_hash = get_hash( $tid );
		$table = 'topic_reply_' . $tid_hash;
		$pre_table = $this->db->dbprefix( 'topic_reply_' . $tid_hash );
		//
		$reply_userid = (int)$reply_userid;
		$z_sql = "SELECT thanks_reply_topic_str FROM `{$pre_table}` WHERE tid = {$tid} AND reply_userid = {$reply_userid} AND id = {$rid} LIMIT 1";
		$tpc = $this->db->query($z_sql)->row_array();

		if( $tpc )
		{
			$arr = explode(',', $tpc['thanks_reply_topic_str']);
			$arr[] = $uid;
			$arr_str = trim( implode(',', $arr), ',');
			//处理完数据在保存
			$this->db->where('id', $rid);
			$this->db->update( $table, array( "$column" => $arr_str) );
		}
		else
		{
			return;
		}

		//-------------------- 事务处理 -------------------------------
		// coin 是否大于 0
		$sql = "UPDATE `{$users_expand}` SET coin=coin-5 WHERE uid={$uid}";
		$ret = $this->db->query($sql);

		$sql = "UPDATE `{$users_expand}` SET coin=coin+5 WHERE uid={$reply_userid}";
		$res = $this->db->query($sql);
		
		if( $ret && $res )
		{
			//hash分表处理
			$uid_hash = get_hash( $uid );
			$table = 'coin_record_' . $uid_hash;
			$record_data = array(
					'uid' => $uid,
					'coin' => -5,
					'desc' => "感谢话题回复",
					'op_type' => 7,
					'random' => random_string(),
					'time' => time(),
				);
			$recrd = $this->db->insert($table, $record_data);

			//hash分表处理
			$reply_userid_hash = get_hash( $reply_userid );
			$table = 'coin_record_' . $reply_userid_hash;
			$record_datas = array(
					'uid' => $reply_userid,
					'coin' => 5,
					'desc' => "{$user_info['name']} 的感谢话题回复",
					'op_type' => 7,
					'random' => random_string(),
					'time' => time(),
				);
			$recrd = $this->db->insert($table, $record_datas);
		}

		//-------------------- 事务处理 END ------------------------------

		return;
	}

	/*
	 * 用户删除自己的话题
	 */
	function del_topic( $tid, $user_info )
	{
		$tid_hash = get_hash( $tid );
		$treply = 'topic_reply_' . $tid_hash;

		// status = 4; topic
		$this->db->where( array('create_userid' => $user_info['id'],'id' => $tid));
		$res1 = $this->db->update( 'topic', array( "status" => 4) );
		// status = 4; topic_reply
		$this->db->where( array('tid' => $tid) );
		$res2 = $this->db->update( $treply, array( "status" => 4) );

		if( $res1 && $res2 )
		{
			return true;	
		}
		else
		{
			return false;
		}
	}

	/*
	 * //判断此主题是否是当前登录用户创建的 --------------------
	 */
	function is_current_user_create_topic( $tid, $uid )
	{
		$res = $this->db->get_where( 'topic', array('create_userid' => $uid,'id' => $tid) )->row_array();
		// echo "<pre>";print_r($res);exit;
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * //  --------------------
	 */
	function set_id_to_hash()
	{
		$res = $this->db->get( 'topic')->result_array();
		
		foreach($res as $k => $v)
		{
			$hashId = md5( 'www.xcspaces.com' . $v['create_username'] . $v['time'] );
			$this->db->where( array('id' => $v['id']) );
			$this->db->update( 'topic', array( "hashId" => $hashId) );
		}
	}

}

?>
