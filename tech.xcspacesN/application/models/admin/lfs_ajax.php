<?php
class Lfs_ajax extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 
	 * 增加次级話題数据
	 */
	function add_topic( $post )
	{
		$node_tree = $this->db->dbprefix("node_tree");

		$id = (int)$post['id'];
		$TopicName = $post['TopicName'];
		$TopicTabName = $post['TopicTabName'];
		$Sort = (int)$post['Sort'];
		$Status = (int)$post['Status'];

		if( empty( $TopicName ) || empty( $TopicTabName ) )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		

		$num = rand(1,5);
		$default_img = config_item('default_img');

		$data = array(
				'fid' => $id,
				'name' => $TopicName,
				'status' => $Status,
				'sort' => $Sort,
				'tab_name' => $TopicTabName,
				'img_path' => $default_img[$num],
				'time' => time(),
			);
		$res = $this->db->insert('node_tree', $data);
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題增加成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題增加失败！');
		}

	}

	/* 
	 * 增加顶级話題数据
	 */
	function add_top_topic( $post )
	{
		$node_tree = $this->db->dbprefix("node_tree");

		$id = (int)$post['id'];
		$TopicName = $post['TopicName'];
		$TopicTabName = $post['TopicTabName'];
		$Sort = (int)$post['Sort'];
		$Status = (int)$post['Status'];

		if( empty( $TopicName ) || empty( $TopicTabName ) )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$num = rand(1,5);
		$default_img = config_item('default_img');

		$data = array(
				'fid' => -1,
				'name' => $TopicName,
				'status' => $Status,
				'sort' => $Sort,
				'tab_name' => $TopicTabName,
				'img_path' => $default_img[$num],
				'time' => time(),
			);
		$res = $this->db->insert('node_tree', $data);
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題增加成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題增加失败！');
		}

	}

	/* 
	 * 增加顶级話題数据
	 */
	function del_topic( $post )
	{
		$node_tree = $this->db->dbprefix("node_tree");

		$id = (int)$post['id'];

		if( $id == 0 )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$this->db->where('zid', $id);
		$res = $this->db->update('node_tree', array( 'status' => 4 ));
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題删除成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題删除失败！');
		}

	}

	/* 
	 * 保存顶级話題数据
	 */
	function save_topic( $post )
	{
		$node_tree = $this->db->dbprefix("node_tree");

		$id = (int)$post['id'];
		$TopicName = $post['TopicName'];
		$TopicTabName = $post['TopicTabName'];
		$Sort = (int)$post['Sort'];
		$Status = (int)$post['Status'];

		if( $id == 0 )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$data = array(
				'name' => $TopicName,
				'status' => $Status,
				'sort' => $Sort,
				'tab_name' => $TopicTabName,
			);

		$this->db->where('zid', $id);
		$res = $this->db->update('node_tree', $data);

		$sql = "SELECT fid FROM `{$node_tree}` WHERE zid=$id";
		$data = $this->db->query( $sql )->row_array();

		if( isset( $data['fid'] ) && $data['fid'] == -1 )
		{
			$this->db->where(array('fid' => $id));
			$sd = array('status' => $Status);
			$this->db->update('node_tree', $sd);
		}
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題保存成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題保存失败！');
		}

	}

	/* 
	 * 修改話題状态
	 */
	function modifyTopicsStatus( $post )
	{
		$topic = $this->db->dbprefix("topic");

		$id = (int)$post['id'];
		$status = (int)$post['status'];

		if( $id <= 0 )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$data = array(
				'status' => $status,
			);

		$this->db->where('id', $id);
		$res = $this->db->update('topic', $data);
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題保存成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題保存失败！');
		}
	}

	/* 
	 * 修改話題状态
	 */
	function modifyTopicsReplyStatus( $post )
	{
		$tid = (int)$post['tid'];
		$rid = (int)$post['rid'];
		$status = (int)$post['status'];

		//hash分表处理
		$tid = (int)$tid;
		
		$hash = get_hash( $tid );
		$table = 'topic_reply_' . $hash;
		//不存在表就创建数据表
		if( !$this->db->table_exists( $table ) )
		{
			return array('res' => 0, 'msg' => '数据表異常！');
		}

		if( $rid <= 0 )
		{
			return array('res' => 0, 'msg' => '參數異常！');
		}

		$data = array(
				'status' => $status,
			);

		$this->db->where('id', $rid);
		$res = $this->db->update($table, $data);
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '話題保存成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '話題保存失败！');
		}
	}

	
}

?>
