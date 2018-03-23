<?php
class Lfs_notifications extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

	/* 设置提醒为已查看 */
	function setNotifications( $uid )
	{
		$notifications = $this->db->dbprefix("notifications");
		$users = $this->db->dbprefix("users");

		$sql = "SELECT id
				FROM `{$users}`
				WHERE id = '{$uid}'
			";

		$users_id = $this->db->query( $sql )->row_array();

		if( !$users_id )
		{
			return false;
		}

		$sql = "UPDATE `{$notifications}` SET  view_status = 1 WHERE rId = {$uid} AND view_status = 0";

		$res = $this->db->query( $sql );

		return $res;
	}
	
}

?>
