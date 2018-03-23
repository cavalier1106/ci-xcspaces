<?php
class Lfs_ajax extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 
	 * 发布主题数据
	 */
	/*function set_publish_topic( $post )
	{
		//{'title' : title, 'content' : content, 'nodeid' : 2, 'uname' : uname, 'uid' : uid}

		$topic = $this->db->dbprefix("topic");
		$node_tree = $this->db->dbprefix("node_tree");

		$title = $post['title'];

		if( empty( $title ) )
		{
			return array('res' => 0, 'msg' => 'title 为空！');
		}

		$zid = (int)$post['nodeid'];
		$sql = "SELECT fid FROM `{$node_tree}` WHERE zid='{$zid}' AND status=1";
		$fid = $this->db->query($sql)->row_array();

		if( !$fid )
		{
			return array('res' => 0, 'msg' => 'zid 不存在！');
		}

		$uid = (int)$post['uid'];
		$uname = $post['uname'];

		$data = array(
				'fid' => $fid['fid'],
				'zid' => $zid,
				'title' => $title,
				'content' => $content, //要优化
				'status' => 0,
				'create_userid' => $uid,
				'create_username' => $uname,
				'time' => time(),
			);
		$res = $this->db->insert('topic', $data);

		// echo "<pre>";print_r($res);exit;
		
		if( $res )
		{
			return array('res' => 1, 'msg' => '主题发布成功！');
		}
		else
		{
			return array('res' => 0, 'msg' => '主题发布失败！');
		}

	}*/

	/* 
	 * Get Tech 主题数据
	 */
	function get_tech_topic()
	{
		$topic = $this->db->dbprefix("topic");
		$page = $this->input->get('page') ? $this->input->get('page'):1;
		$pageSize = $this->input->get('pageSize') ? $this->input->get('pageSize'):6;

		$offset = ($page - 1) * $pageSize;
		$sql = "SELECT * FROM `{$topic}` WHERE fid='1' AND status=1 LIMIT {$offset},{$pageSize}";
		$techTopic = $this->db->query($sql)->result_array();

		foreach ($techTopic as $key => $value) {
			if(!strip_tags($value['content'])){
				$value['content'] = $value['title'] . '...';
			}else{
				$value['content'] = strCut($value['content'], 90);
			}
		}

		return $techTopic;
	}

	/* 
     * 保存内部用户号到文件
     */
	function set_user_to_file()
	{
		//addslashes() 如果CI框架本身有处理过的话,就不用在作处理 --- 安全;
		
		$users = $this->db->dbprefix("users");
		$sql = "SELECT * FROM `{$users}` WHERE user_type=2";
		$res = $this->db->query($sql)->result_array();

		$size = file_put_contents('./doc/user_type_2.json', json_encode($res));

		return $size;
	}

	/* 
     * 读取内部用户号文件
     */
	function get_user_to_file()
	{
		$res = file_get_contents('./doc/user_type_2.json');
		$res = json_decode($res, true);

		return $res;
	}
	
}

?>
