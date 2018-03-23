<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('getUserInfo'))
{
	function getUserInfo( $uname = '' )
	{
		/*
		 * @查看用户信息;
		 */
		$ci = & get_instance();

		if( empty( $uname ) )
		{
			return array();
		}

		$sql = "SELECT u.*, t.*, e.gold, e.silver, e.coin, e.expand1, e.expand2
				FROM `{$ci->db->dbprefix('users')}` u 
				LEFT JOIN `{$ci->db->dbprefix('topic_user')}` as t ON t.uid = u.id
				LEFT JOIN `{$ci->db->dbprefix('users_expand')}` as e ON e.uid = u.id
				WHERE u.name='{$uname}' 
				LIMIT 1
			";
		$data = $ci->db->query($sql)->row_array();

		// 不存在话题ID,可能是随便输入数据
		if( empty( $data ) )
		{
			error_404();
		}

		//收藏空间总数
		if( $data['nodes_str'] )
		{
			$nodes_str_nums = count( explode( ',', $data['nodes_str'] ) );
		}
		else
		{
			$nodes_str_nums = 0;
		}
		//收藏主题总数
		if( $data['titles_str'] )
		{
			$titles_str_nums = count( explode( ',', $data['titles_str'] ) );
		}
		else
		{
			$titles_str_nums = 0;
		}
		//特别关注用户总数
		if( $data['users_str'] )
		{
			$users_str_nums = count( explode( ',', $data['users_str'] ) );
		}
		else
		{
			$users_str_nums = 0;
		}

		if( !$data['silver'] )
		{
			$data['silver'] = 0;
		}
		
		if( !$data['coin'] )
		{
			$data['coin'] = 0;
		}
		
		$data['nodes_str_nums'] = $nodes_str_nums;
		$data['titles_str_nums'] = $titles_str_nums;
		$data['users_str_nums'] = $users_str_nums;

		// echo "<pre>";var_dump( $data );exit;

		return $data;
	}
}

if( ! function_exists('getUserInfo2'))
{
	function getUserInfo2( $uid = 0 )
	{
		/*
		 * @查看用户信息;
		 */
		$ci = & get_instance();

		if( (int)$uid == 0 )
		{
			return array();
		}

		$sql = "SELECT u.*, u.img_path as avatar, t.*, e.gold, e.silver, e.coin, e.expand1, e.expand2
				FROM `{$ci->db->dbprefix('users')}` as u 
				LEFT JOIN `{$ci->db->dbprefix('topic_user')}` as t ON t.uid = u.id
				LEFT JOIN `{$ci->db->dbprefix('users_expand')}` as e ON e.uid = u.id
				WHERE u.id='{$uid}' 
				LIMIT 1
			";
		$data = $ci->db->query($sql)->row_array();

		// 不存在话题ID,可能是随便输入数据
		if( empty( $data ) )
		{
			error_404();
		}

		//收藏空间总数
		if( $data['nodes_str'] )
		{
			$nodes_str_nums = count( explode( ',', $data['nodes_str'] ) );
		}
		else
		{
			$nodes_str_nums = 0;
		}
		//收藏主题总数
		if( $data['titles_str'] )
		{
			$titles_str_nums = count( explode( ',', $data['titles_str'] ) );
		}
		else
		{
			$titles_str_nums = 0;
		}
		//特别关注用户总数
		if( $data['users_str'] )
		{
			$users_str_nums = count( explode( ',', $data['users_str'] ) );
		}
		else
		{
			$users_str_nums = 0;
		}

		if( !$data['silver'] )
		{
			$data['silver'] = 0;
		}
		
		if( !$data['coin'] )
		{
			$data['coin'] = 0;
		}
		
		$data['nodes_str_nums'] = $nodes_str_nums;
		$data['titles_str_nums'] = $titles_str_nums;
		$data['users_str_nums'] = $users_str_nums;

		// echo "<pre>";var_dump( $data );exit;

		return $data;
	}
}

if( ! function_exists('dailyCoinIsGet') )
{
	function dailyCoinIsGet( $uname )
	{
		$ci = & get_instance();

		// $ci->db = $ci->load->database('xcs_coin_record_read', true);

		/*
		 * 领取今日的登录奖励
		 */
		$time = strtotime( date('Y-m-d') );

		$sql = "SELECT id
				FROM `{$ci->db->dbprefix('users')}`
				WHERE name='{$uname}'
				LIMIT 1
			";
		$res = $ci->db->query($sql)->row_array();

		if( !$res )
		{
			return false;
		}

		//hash分表处理
		$uid = (int)$res['id'];
		$uid_hash = get_hash( $uid );
		$table = 'coin_record_' . $uid_hash;
		create_coin_record_table( $uid_hash );

		$sql = "SELECT u.id
				FROM `{$ci->db->dbprefix('users')}` as u
				LEFT JOIN `{$ci->db->dbprefix($table)}` as c ON c.uid = u.id
				WHERE u.name='{$uname}' AND c.time >= $time AND c.op_type = 1
				LIMIT 1
			";
		if( $ci->db->query($sql)->row_array() )
		{
			return true;
		}
		else
		{
			return false;
		}


	}
}

/* 
 * --------------------- 获取用户威望排名信息 -----------------------------
 */
if( ! function_exists('getUserCoinRank') )
{
	function getUserCoinRank()
	{
		$ci = & get_instance();

		$sql = "SELECT u.id,u.name,u.webname,u.img_path,u.user_avatar,ue.coin
				FROM `{$ci->db->dbprefix('users_expand')}` ue
				LEFT JOIN `{$ci->db->dbprefix('users')}` as u ON u.id = ue.uid
				ORDER BY ue.coin DESC
				LIMIT 10
			";

		$res = $ci->db->query($sql)->result_array();

		if( $res )
		{
			// $uids = '';
			// foreach ($res as $k => $v) 
			// {
			// 	$uids .= $v['uid'] . ',';
			// }
			foreach ($res as &$v) 
			{
				$img = json_decode( unserialize( $v['user_avatar'] ), true );
				$v['imgNormal'] = $img['normal'];
				unset($v['user_avatar']);
			}
			return $res;
		}
		else
		{
			return array();
		}
	}
}

/* 
 * --------------------- 当前登陆用户是否感谢 -----------------------------
 */
if( ! function_exists('is_thanks_reply_uid') )
{
	function is_thanks_reply_uid( $reply_user_str = '', $uid = 0 ) {
		$arr = explode(',', $reply_user_str);
		if( in_array($uid, $arr) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}


/* 
 * --------------------- 获取用户威望排名信息 -----------------------------
 */
if( ! function_exists('save_filter_ip') )
{
	function save_filter_ip( $real_ip, $uname )
	{
		$ci = & get_instance();

		$data = array(
			'ip' => ip2long( $real_ip ),
			'userName' => $uname,
			'status' => 2,
			'time' => time(),
			);
		
		$sql = "INSERT INTO `{$ci->db->dbprefix('filter_ip')}`(ip,userName,status,time)  VALUES ('{$data[ip]}','{$data[userName]}','{$data[status]}','{$data[time]}')";

		$res = $ci->db->query($sql);
		
		// $res = $ci->db->insert('filter_ip', $data);

		if( $res )
		{
			return '数据保存成功！';
		}
		else
		{
			return '数据保存失败！';
		}
	}
}


/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */