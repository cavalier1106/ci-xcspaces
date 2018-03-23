<?php
class Lfs_ads extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database('xcs_write', true);
    }

    /* 置顶广告 */
	function get_top_ads( $tab='' )
	{
		$ads = $this->db->dbprefix("ads");

		$sql = "SELECT *
				FROM `{$ads}`
				WHERE status=1 AND logo_type=1
				ORDER BY sort DESC
				LIMIT 1
			";
		$res = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($res);exit;

		return $res;
	}

	/* 根据子空间获取相关广告 */
	function get_ads( $znode_name )
	{
		$node_tree = $this->db->dbprefix("node_tree");
		$ads = $this->db->dbprefix("ads");

		$sql = "SELECT zid
				FROM `{$node_tree}`
				WHERE status=1 AND tab_name='{$znode_name}'
				limit 1
			";
		$zid = $this->db->query($sql)->row_array();

		// echo "<pre>";print_r($zid);exit;

		$sql = "SELECT * FROM `{$ads}` WHERE status=1 AND logo_type!=1 ORDER BY sort desc";

		$ads_list = $this->db->query($sql)->result_array();

		foreach($ads_list as $k=>$v)
		{
			$zid_arr = explode(',', $v['zid_str']);

			if( !in_array($zid['zid'], $zid_arr) )
			{
				unset( $ads_list[$k] );
			}
		}

		// echo "<pre>";print_r($ads_list);exit;

		return $ads_list;
	}

	/* 根据详细页面主题id获取相关广告 */
	function get_topic_ads( $tid )
	{
		$ads = $this->db->dbprefix("ads");

		$sql = "SELECT * FROM `{$ads}` WHERE status=1 AND logo_type!=1 ORDER BY sort desc";

		$ads_list = $this->db->query($sql)->result_array();

		foreach($ads_list as $k=>$v)
		{
			$zid_arr = explode(',', $v['topic_str']);

			if( !in_array($tid, $zid_arr) )
			{
				unset( $ads_list[$k] );
			}
		}

		// echo "<pre>";print_r($ads_list);exit;

		return $ads_list;
	}
	
}

?>
