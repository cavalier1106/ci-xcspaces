<?php
class Lfs_topic_clicks_details extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 
     * 主题页面点击数统计 
     * 主题点击次数
     */
	function set_topic_clicks_details( $tid, $uname )
	{
		//hash分表处理
		$tid = (int)$tid;
		
		$hash = get_hash( $tid );

		$table = 'topic_clicks_details_' . $hash;

		$topic_clicks = $this->db->dbprefix("topic_clicks");

		$sql = "SELECT tid FROM `{$topic_clicks}` WHERE tid={$tid}";
		if( $this->db->query($sql)->row_array() )
		{
			//获取标签ZID
			$sql = "UPDATE `{$topic_clicks}` SET clicks=clicks+1 WHERE tid=$tid";
			$res = $this->db->query($sql);
		}
		else
		{
			$data = array(
				'tid' => $tid,
				'clicks' => 1,
			);
			$ret = $this->db->insert('topic_clicks', $data);
		}

		/*
		$tables_list = $this->db->list_tables();

		if( !in_array($table, $tables_list) )
		{
			$res = create_topic_clicks_details_table( $hash );
		}*/
		
		$this->wdb = $this->load->database('xcs_topic_clicks_detail_write', true);

		if( !$this->wdb->table_exists( $table ) )
		{
			$res = create_topic_clicks_details_table( $hash );
		}

		$ip = real_ip();
		$data = array(
				'tid' => $tid,
				'uname' => $uname,
				'random' => random_string(),
				'view_ip' => $ip,
				'time' => time(),
			);
		$ret = $this->wdb->insert($table, $data);

		return $ret;
	}

}

?>
