<?php
class Lfs_feedback extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

    /* 保存用户信息 */
	function set_feedback( $uname='', $post_data )
	{
		if( $post_data['content'] == '' )
		{
			return array('code' => 1,'msg' => '正文不能为空！');
		}

		if( !empty( $uname ) )
		{
			$res = $this->db->get_where('users', array('name' => $uname))->row_array();
			$fb_create_userid = $res['id'];
		}
		else
		{
			$fb_create_userid = 0;
			$uname = '游客';
		}

		$data = array(
				'fb_content' => $post_data['content'],
				'fb_status' => 1,
				'fb_create_userid' => $fb_create_userid,
				'fb_create_username' => $uname,
				'fb_time' => time(),
			);

		if( $this->db->insert('feedback', $data) )
		{
			return array('code' => 1,'msg' => '反馈发送成功！');
		}
	}

	
}

?>
