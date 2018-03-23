<?php
class Lfs_topic extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* xcspaces 的话题 */
	function get_one_topic_info( $tid = 0 )
	{
		if( $tid == 0 )
		{
			return array();
		}
		$topic = $this->db->dbprefix("topic");

		//查找xcspaces 空间的话题

		$sql = "SELECT id,title,keyworks,description,status
				FROM `{$topic}`
				WHERE id={$tid}
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 
	 * 增加顶级話題数据
	 */
	function edit_topic( $post )
	{
		$topic = $this->db->dbprefix("topic");

		if( isset( $post['id'] ) )
		{
			$id = (int)$post['id'];
		}
		else
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		if( $id == 0 )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$data = array( 
				'title' => $post['Title'],
				'status' => $post['Status'],
				'keyworks' => $post['keyWorks'],
				'description' => $post['Description'],
			);
		$this->db->where('id', $id);
		$res = $this->db->update('topic', $data);
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題编辑成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題编辑失败！');
		}

	}

	/* 对应xcspaces 的话题 */
	function get_topic_no_audit_total()
	{
		$topic = $this->db->dbprefix("topic");

		//查找对应xcspaces 空间的话题

		$sql = "SELECT count(*) as nums
				FROM `{$topic}`
				-- WHERE status!=1
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	function get_topic_no_audit( $page, $num, $order='DESC' ){

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		$topic = $this->db->dbprefix("topic");

		$order = "time {$order}";

		$sql = "SELECT id,title,status,content,time
				FROM `{$topic}`
				-- WHERE status!=1 
				ORDER BY {$order}
				LIMIT {$offset},{$num}
		";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	/* 对应xcspaces 的话题 */
	function get_topic_reply_no_audit_total( $tid = 0 )
	{
		//hash分表处理
		$tid = (int)$tid;
		
		$hash = get_hash( $tid );
		$table = 'topic_reply_' . $hash;
		//不存在表就创建数据表
		if( !$this->db->table_exists( $table ) )
		{
			return array();
		}

		$topic_reply = $this->db->dbprefix( $table );

		//查找对应xcspaces 空间的话题

		$sql = "SELECT count(*) as nums
				FROM `{$topic_reply}`
				WHERE tid='{$tid}'
			";
		$data = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

	function get_topic_reply_no_audit( $tid, $page, $num, $order='DESC' ){

		//hash分表处理
		$tid = (int)$tid;
		
		$hash = get_hash( $tid );
		$table = 'topic_reply_' . $hash;
		//不存在表就创建数据表
		if( !$this->db->table_exists( $table ) )
		{
			return array();
		}

		$topic_reply = $this->db->dbprefix( $table );

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		$order = "time {$order}";

		$sql = "SELECT id,status,content,reply_username,time
				FROM `{$topic_reply}`
				WHERE tid='{$tid}'
				ORDER BY {$order}
				LIMIT {$offset},{$num}
		";
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);exit;

		return $data;
	}

}

?>
