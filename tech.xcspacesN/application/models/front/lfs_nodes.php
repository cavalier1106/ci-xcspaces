<?php
class Lfs_nodes extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 递归获取空间 */
	function get_nodes()
	{
		$node_tree = $this->db->dbprefix("node_tree");
		$sql = "SELECT zid,fid,name,tab_name,sort,status,img_path,title,keyworks,description FROM `{$node_tree}` WHERE fid=-1 AND status=1 AND tab_name='tech' ORDER BY sort desc";
		$res = $this->db->query($sql)->result_array();
		$nodes = array();
		foreach($res as $k => $v)
		{
			$z_sql = "SELECT zid,fid,name,tab_name,sort,status,img_path,title,keyworks,description FROM `{$node_tree}` WHERE fid={$v['zid']} AND status=1 ORDER BY sort desc";
			$znodes = $this->db->query($z_sql)->result_array();
			// $nodes[$v['tab_name']]['fid'] = $v;
			$nodes['zid'] = $znodes;
		}
		// echo "<pre>";print_r($nodes);exit;
		return $nodes;
	}

	/* 
	 * 根据子空间ID获取相应信息 
	 */
	function get_znode_info( $zid )
	{
		$node_tree = $this->db->dbprefix("node_tree");
		$sql = "SELECT tab_name FROM `{$node_tree}` WHERE zid=$zid LIMIT 1";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		if( $data )
		{
			return $data['tab_name'];
		}
		else
		{
			return '';
		}
	}

	/* 
	 * 获取2级空间 
	 */
	function get_znodes()
	{
		$node_tree = $this->db->dbprefix("node_tree");

		$sql = "SELECT zid,name,tab_name FROM `{$node_tree}` WHERE fid!=-1 AND status=1 AND zid=1 ORDER BY sort desc";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 获取1-2级空间缓存 
	 */
	function get_znodes_list()
	{
		$node_tree = $this->db->dbprefix("node_tree");
		$out_data = array();

		if($this->redis->get('tech_node_tree_data'))
		{
			$out_data = unserialize($this->redis->get('tech_node_tree_data'));
			return $out_data;
		}

		$sql = "SELECT zid,name,tab_name FROM `{$node_tree}` WHERE fid=-1 AND status=1 AND tab_name='tech' ORDER BY sort desc";
		$data = $this->db->query($sql)->result_array();

		
		foreach ($data as $k => $v) 
		{
			$out_data[$k]['zid'] = $v['zid'];
			$out_data[$k]['name'] = $v['name'];
			$out_data[$k]['tab_name'] = $v['tab_name'];

			$sql = "SELECT zid,name,tab_name FROM `{$node_tree}` WHERE fid='{$v['zid']}' AND status=1 ORDER BY sort desc";
			$zd = $this->db->query($sql)->result_array();
			$out_data[$k]['children'] = $zd;
		}

		$this->redis->set('tech_node_tree_data', serialize($out_data), config_item('USER_NOTETREE_SAVE_TIME'));

		// echo "<pre>";print_r($out_data);exit;

		return $out_data;
	}

	/* 
	 * 获取2级空间 group by
	 */
	function get_znodes_new()
	{
		$node_tree = $this->db->dbprefix("node_tree");
		$sql = "SELECT zid,fid,name,tab_name FROM `{$node_tree}` WHERE fid!=-1 AND status=1 ORDER BY sort desc";
		$data = $this->db->query($sql)->result_array();
		$out_data = array();
		foreach($data as $k => $v)
		{
			$out_data[$v['fid']][] = $v;
		}
		echo "<pre>";print_r($out_data);exit;

		return $data;
	}

	/* 今日热门空间--今日主题数统计 */
	function get_hot_nodes()
	{
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		// $time = strtotime( date('Y-m-d') . ' 00:00:00' );
		$time = strtotime( '-90 day' );

		//统计出今日回复热贴
		$sql = "SELECT zid, count(zid) as topic_counts
				FROM `{$topic}`
				WHERE status=1 AND time >= {$time} AND fid=1
				group by zid 
				ORDER BY topic_counts desc
				LIMIT 20
			";
		$topic_data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($topic_data);exit;
		
		$znodes_arr = array();
		foreach($topic_data as $k => $v)
		{
			$z_sql = "SELECT name, tab_name FROM `{$node_tree}` WHERE zid={$v['zid']} AND status=1";
			$znodes = $this->db->query($z_sql)->row_array();
			$znodes_arr[$k]['name'] = $znodes['name'];
			$znodes_arr[$k]['tab_name'] = $znodes['tab_name'];
		}

		return $znodes_arr;
	}

	/* 最近新加空间/标签 */
	function get_new_nodes()
	{
		//最近 默认3天前添加空间时间
		$time = strtotime('-30 day');

		$node_tree = $this->db->dbprefix("node_tree");
		$z_sql = "SELECT zid, name, tab_name FROM `{$node_tree}` WHERE fid!=-1 AND status=1 AND time >= {$time} ORDER By time DESC LIMIT 20";
		$znodes_arr = $this->db->query($z_sql)->result_array();

		// echo "<pre>";print_r($znodes_arr);exit;

		return $znodes_arr;
	}
	
	/* 子空间关联空间 */
	function get_relate_nodes( $znode_name )
	{
		$node_tree = $this->db->dbprefix("node_tree");
		//获取父ID
		$z_sql = "SELECT fid FROM `{$node_tree}` WHERE tab_name = '{$znode_name}' AND status = 1 LIMIT 1";
		$fid = $this->db->query($z_sql)->row_array();

		//获相关子空间
		$z_sql = "SELECT zid, name, tab_name, img_path FROM `{$node_tree}` WHERE fid = {$fid['fid']} AND status = 1";
		$nodes = $this->db->query($z_sql)->result_array();

		return $nodes;
	}

	/* 当前子空间信息 */
	function get_node_info( $znode_name )
	{
		$node_tree = $this->db->dbprefix("node_tree");
		//获取子空间信息
		$z_sql = "SELECT * FROM `{$node_tree}` WHERE tab_name = '{$znode_name}' AND status = 1 LIMIT 1";
		$node_info = $this->db->query($z_sql)->row_array();
		//获取父空间信息
		$z_sql = "SELECT name as f_name, tab_name as f_tab_name, img_path as avatar FROM `{$node_tree}` WHERE zid = {$node_info['fid']} AND status = 1 LIMIT 1";
		$f_name = $this->db->query($z_sql)->row_array();

		$node_info['f_name'] = $f_name['f_name'];
		$node_info['f_tab_name'] = $f_name['f_tab_name'];
		$node_info['avatar'] = $f_name['avatar'];
		
		// echo "<pre>";print_r($node_info);exit;

		return $node_info;
	}

	 /* 根据主题ID获取zid,在根据zid获取子空间名 */
	function get_znode_name( $tid )
	{
		$this->load->database('xcs_read', true);
		
		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		$sql = "SELECT n.tab_name
				FROM `{$topic}` as t
				LEFT JOIN `{$node_tree}` as n ON t.fid=n.zid
				WHERE t.id='{$tid}' AND n.status=1 
				LIMIT 1
			";
		$data = $this->db->query($sql)->row_array();

		// 不存在话题ID,可能是随便输入数据
		if( empty( $data ) )
		{
			error_404();
		}

		// echo "<pre>";print_r($data);exit;

		return $data['tab_name'];
	}

	/* 
	 * ONE主题空间面包屑信息 
	 */
	function get_one_topic_crumb( $tid = 0 )
	{
		if( (int)$tid == 0 )
		{
			return array();
		}

		$node_tree = $this->db->dbprefix("node_tree");
		$topic = $this->db->dbprefix("topic");

		//获取主题对应子空间信息(只有两级)
		$z_sql = "SELECT fid,zid FROM `{$topic}` WHERE id = '{$tid}' AND status = 1 LIMIT 1";
		$node_info = $this->db->query($z_sql)->row_array();

		$zid = $node_info['zid'];
		$fid = $node_info['fid'];

		//获取子空间信息
		$z_sql = "SELECT name as z_name, tab_name as z_tab_name FROM `{$node_tree}` WHERE zid = '{$zid}' AND status = 1 LIMIT 1";
		$z_node_info = $this->db->query($z_sql)->row_array();

		//获取父空间信息
		$z_sql = "SELECT name as f_name, tab_name as f_tab_name FROM `{$node_tree}` WHERE zid = '{$fid}' AND status = 1 LIMIT 1";
		$f_node_info = $this->db->query($z_sql)->row_array();
		
		$data = array_merge($z_node_info, $f_node_info);
		
		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 我的收藏空间或是标签总数
	 */
	function collection_nodes_count( $uid = 0 )
	{
		if( $uid == 0 )
		{
			return 0;
		}

		$topic_user = $this->db->dbprefix("topic_user");

		//查找对应空间的主题

		$sql = "SELECT nodes_str
				FROM `{$topic_user}`
				WHERE uid={$uid}
			";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( empty( $res ) )
		{
			return 0;
		}

		$data = explode(',', $res['nodes_str']);

		// echo "<pre>";print_r($data);exit;

		return count( $data );
	}

	/* 
	 * 我的收藏空间或是标签列表
	 */
	function collection_nodes( $uid = 0, $page = 1, $setting )
	{
		if( $uid == 0 )
		{
			return array();
		}

		$topic_user = $this->db->dbprefix("topic_user");
		$node_tree = $this->db->dbprefix("node_tree");
		$topic = $this->db->dbprefix("topic");

		//查找对应空间的主题

		$sql = "SELECT nodes_str FROM `{$topic_user}` WHERE uid={$uid}";
		$res = $this->db->query($sql)->row_array();

		//防止乱输入用户ID
		if( !$res )
		{
			return array( 'nums' => 0, 'data' => array() );
		}

		$nid_arr = array();
		
		if( !empty( $res['nodes_str'] ) ) $nid_arr = explode(',', $res['nodes_str']);

		$nums = count( $nid_arr );

		//分页取空间数据
		$num = $setting['per_page'];
		$offset = ( $page - 1 ) * $num;
		if( $offset == '' )
		{
			$offset = 0;
		}

		$nid_arr = array_slice($nid_arr, $offset, $num);

		if( !$nid_arr )
		{
			return array('nums' => $nums, 'data' => array());
		}

		$nid_str = implode(',', $nid_arr);

		$sql = "SELECT * FROM `{$node_tree}` WHERE status=1 AND zid IN({$nid_str}) ORDER BY time DESC";
		$data = $this->db->query($sql)->result_array();
		
		// -------------------------日后优化 -------------------------
		foreach( $data as $k => &$v )
		{
			$sql = "SELECT count(*) as nums FROM `{$topic}` WHERE status=1 AND zid={$v['zid']}";
			$ret = $this->db->query( $sql )->row_array();
			$v['topic_counts'] = $ret['nums'];
		}
		// echo "<pre>";print_r($data);exit;

		return array('nums' => $nums, 'data' => $data);
	}
	

}

?>
