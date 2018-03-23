<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('rand'))
{
	/**
	 * 产生随机数
	 *
	 * @static 
	 * @access public
	 * @param int	$min	随机数最小值范围
	 * @param int	$max	随机数最大值范围
	 * @return string
	 */
	function rand($min = null, $max = null)
	{	
		if (isset($min) && isset($max)){
			if ($min >= $max){
				return $min;
			}else{
				return mt_rand($min, $max);
			}
		}else{
			return mt_rand();
		}
	}
}

if( ! function_exists('strCut'))
{
	function strCut($str,$length)//$str为要进行截取的字符串，$length为截取长度（汉字算一个字，字母算半个字）
	{
		$str = trim( strip_tags( $str ) );
		$string = "";
		if(strlen($str) > $length)
		{
			for($i = 0 ; $i<$length ; $i++)
			{
				if(ord($str{$i}) > 127)
				{
					$string .= $str[$i] . $str[$i+1] . $str[$i+2];
					$i = $i + 2;
				}
				else
				{
					$string .= $str[$i];
				}
			}
			$string .= "...";
			return $string;
		}
		return $str;
	}
}

if( ! function_exists('generate_rand_character'))
{
	/**
	* @function generate_rand_character
	* 
	* @param int $length 随机字符串长度
	* @param int $base_str 基础字符集
	* @return  string 随机字符串
	*/
	function generate_rand_character($length=16,$base_str='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'){
		if(!$length) $length=10;
		if(!$base_str) $base_str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$max = strlen($base_str) - 1;
		mt_srand( (double) microtime()*1000000 );
		for($i=0; $i<$length; $i++)
		{
		    $hash .= $base_str[mt_rand(0, $max)];
		}

		return $hash;
	}
}

if( ! function_exists('makekeys'))
{
	/*
	*@ 对字符串进行处理，首先把换行转为空格，然后以空格为单位生成数组，去除重复空值，下标从0开始
	*/
	function makekeys($str){   
		$arr = array("\t", "\n", "\r","\r\n");
		$replace = " ";
		$str=str_replace($arr, $replace, $str);  //换行改成空格
		$c=explode(" ",$str);
		
		$c=array_unique($c);  //去除重复
		$info=array_filter($c);	//去除空值
		rsort($info);            //下标从0开始
		
		return $info;
	}	
}

if( ! function_exists('makekey'))
{
	/*
	*@ 对字符串进行处理，以换行为单位生成数组，去除重复空值，下标从0开始
	*/
	function makekey($str){   
			$arr = array("\t", "\n", "\r","\r\n");
			//$replace = " ";
			//$str=str_replace($arr, $replace, $str);  //换行改成空格
			//$c=explode(" ",$str);
			$c=multiexplode($arr,$str);
			$c=array_unique($c);  //去除重复
			$info=array_filter($c);	//去除空值
			rsort($info);            //下标从0开始
			return $info;
	}
}

if( ! function_exists('multiexplode') )
{
	function multiexplode ($delimiters,$string){
		//以换行为单位生成数组
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return  $launch;
	}
}

/* 
 * --------------------- 获取ERROR页面 -----------------------------
 */
if( ! function_exists('error_404') )
{
	function error_404( )
	{
		ob_start();
		include(APPPATH.'views/xcspace/error_404.php');
		$buffer = ob_get_contents();
		ob_end_clean();

		echo $buffer;
		exit();
	}
}
/* 
 * --------------------- 获取 几天前 -----------------------------
 */
if( ! function_exists('xcspaces_format_date') )
{
	function xcspaces_format_date( $time ) 
	{
	    $nowtime = time();
	    $difference = $nowtime - $time;

	    switch ( $difference ) {

	        case $difference <= '60' :
	            $msg = '刚刚';
	            break;

	        case $difference > '60' && $difference <= '3600' :
	            $msg = floor($difference / 60) . '分钟前';
	            break;

	        case $difference > '3600' && $difference <= '86400' :
	            $msg = floor($difference / 3600) . '小时前';
	            break;

	        case $difference > '86400' && $difference <= '2592000' :
	            $msg = floor($difference / 86400) . '天前';
	            break;

	        case $difference > '2592000' &&  $difference <= '7776000':
	            $msg = floor($difference / 2592000) . '个月前';
	            break;
	        case $difference > '7776000':
	            $msg = '很久以前';
	            break;
	    }

	    return $msg;
	}
}

/* 
 * --------------------- 分表获取hash值 -----------------------------
 */
if( ! function_exists('get_hash') )
{
	function get_hash( $tid )
	{
		$str = bin2hex( $tid );
		$hash = substr($str, 0, 3);
		if ( strlen( $hash ) < 6 )
		{
			$hash = str_pad($hash, 6, "0");
		}

		return $hash;
	}
}

/* 
 * --------------------- 分表获取求余值 -----------------------------
 */
if( ! function_exists('get_mod') )
{
	function get_mod( $id )
	{
		$id = $id % 10000;

		return $id;
	}
}

/* 
 * --------------------- 头像上传路径设置 -----------------------------
 */
if( ! function_exists('make_dir') )
{
	function make_dir($dir, $index = true) {
		$res = true;
		if(!is_dir($dir)) {
			$res = @mkdir($dir, 0777);
			$index && @touch($dir.'/index.html');
		}
		return $res;
	}
}

/* 
 * --------------------- 阅读单位换算 -----------------------------
 */
if( ! function_exists('getClickUnit') )
{
	function getClickUnit( $click ) {

		if( $click >= 10000 )
		{
        	$xcs_click = number_format($click/10000, 1);
        	$xcs_click .= $xcs_click.'w';
        }
        elseif( $click >= 1000 )
        {
        	$xcs_click = number_format($click/1000, 1);
        	$xcs_click .= $xcs_click.'k';
        }
        else
        {
        	$xcs_click = $click;
        }

		return $xcs_click;
	}
}

/* 
 * --------------------- 威望值单位换算 -----------------------------
 */
if( ! function_exists('getCoinUnit') )
{
	function getCoinUnit( $coin ) {

		if( $coin >= 10000 )
		{
        	$xcs_coin = number_format($coin/10000, 1);
        	$xcs_coin .= $xcs_coin.'w';
        }
        elseif( $coin >= 1000 )
        {
        	$xcs_coin = number_format($coin/1000, 1);
        	$xcs_coin .= $xcs_coin.'k';
        }
        else
        {
        	$xcs_coin = $coin;
        }

		return $xcs_coin;
	}
}

/* 
 * --------------------- 未读提醒 -----------------------------
 */
if( ! function_exists('get_notifications2') )
{
	function get_notifications2( $uname, $is_getData = 1, $page = 1, $num = 20 )
	{
		/*
		 * @查看用户话题;
		 */
		$ci = & get_instance();
		$sql = "SELECT id
				FROM `{$ci->db->dbprefix('topic')}`
				WHERE create_username='{$uname}' 
			";
		$data = $ci->db->query( $sql )->result_array();
		
		$tid_arr = array();
		foreach ($data as $k => $v) 
		{
			$tid_arr[] = $v['id'];
		}

		// 未读提醒数量
		if( $is_getData == 1 )
		{
			if( empty( $tid_arr ) )
			{
				return 0;
			}

			if( !$ci->db->table_exists( 'view_topic_reply' ) )
			{
				return 0;
			}

			$sql = "SELECT count(*) as nums
				FROM `view_topic_reply`
				WHERE view_status = 0 AND status = 1 AND tid IN (" . implode(',', $tid_arr) . ")
			";
			$res = $ci->db->query( $sql )->row_array();

			return (int)$res['nums'];
		}

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		// 查看系统提醒总数
		if( $is_getData == 2 )
		{
			/* -----------------------------------------------------------------------
			 * ---分表->提醒系统【根据相应用户更新表topic_reply字段view_status为1】---
			 * -----------------------------------------------------------------------
			 */
			alter_topic_reply( $tid_arr );

			if( !$tid_arr )
			{
				return 0;
			}

			if( !$ci->db->table_exists( 'view_topic_reply' ) )
			{
				return 0;
			}

			$sql = "SELECT count(*) as nums
				FROM `view_topic_reply` v
				LEFT JOIN `{$ci->db->dbprefix('topic')}` as t ON t.id = v.tid
				LEFT JOIN `{$ci->db->dbprefix('users')}` as u ON t.create_userid = u.id
				WHERE v.status = 1 AND t.status = 1 AND v.tid IN (" . implode(',', $tid_arr) . ")
			";

			$res = $ci->db->query( $sql )->row_array();

			return (int)$res['nums'];
		}

		// 分页查看系统提醒数据
		if( $is_getData == 3 )
		{
			if( !$tid_arr )
			{
				return array();
			}

			if( !$ci->db->table_exists( 'view_topic_reply' ) )
			{
				return array();
			}

			$sql = "SELECT v.*, t.title, u.id as uid, u.name, u.img_path as avatar
				FROM `view_topic_reply` v
				LEFT JOIN `{$ci->db->dbprefix('topic')}` as t ON t.id = v.tid
				LEFT JOIN `{$ci->db->dbprefix('users')}` as u ON t.create_userid = u.id
				WHERE v.status = 1 AND t.status = 1 AND v.tid IN (" . implode(',', $tid_arr) . ") 
				LIMIT {$offset},{$num}
			";

			$res = $ci->db->query( $sql )->result_array();

			return $res;
		}

	}
}

/* 
 * --------------------- 未读提醒 -----------------------------
 */
if( ! function_exists('get_notifications') )
{
	function get_notifications( $uname, $is_getData = 1, $page = 1, $num = 20 )
	{
		$ci = & get_instance();

		// 未读提醒数量
		if( $is_getData == 1 )
		{
			$notifications = $ci->db->dbprefix("notifications");
			$users = $ci->db->dbprefix("users");

			$sql = "SELECT id
					FROM `{$users}`
					WHERE name = '{$uname}'
				";

			$users_id = $ci->db->query( $sql )->row_array();

			if( !$users_id )
			{
				return 0;
			}

			$sql = "SELECT count(*) as nums
					FROM `{$notifications}`
					WHERE view_status = 0 AND rId = '{$users_id['id']}'
				";

			$res = $ci->db->query( $sql )->row_array();

			return (int)$res['nums'];
		}

		$offset = ( $page - 1 ) * $num;

		if( $offset == '' )
		{
			$offset = 0;
		}

		// 查看系统提醒总数
		if( $is_getData == 2 )
		{
			$notifications = $ci->db->dbprefix("notifications");
			$users = $ci->db->dbprefix("users");

			$sql = "SELECT id
					FROM `{$users}`
					WHERE name = '{$uname}'
				";

			$users_id = $ci->db->query( $sql )->row_array();

			if( !$users_id )
			{
				return 0;
			}

			$sql = "SELECT count(*) as nums
					FROM `{$notifications}`
					WHERE rId = '{$users_id['id']}'
				";

			$res = $ci->db->query( $sql )->row_array();

			return (int)$res['nums'];
		}

		// 分页查看系统提醒数据
		if( $is_getData == 3 )
		{
			$notifications = $ci->db->dbprefix("notifications");
			$users = $ci->db->dbprefix("users");

			$sql = "SELECT id
					FROM `{$users}`
					WHERE name = '{$uname}'
				";

			$users_id = $ci->db->query( $sql )->row_array();

			if( !$users_id )
			{
				return array();
			}

			$sql = "SELECT *
					FROM `{$notifications}`
					WHERE rId = '{$users_id['id']}'
					LIMIT {$offset},{$num}
				";

			$res = $ci->db->query( $sql )->result_array();

			return $res;
		}

	}
}

/* 
 * --------------------- 话题 hashId 映射 id  -----------------------------
 */
if( ! function_exists('topic_hashId_to_id') )
{
	function topic_hashId_to_id( $hashId )
	{
		$ci = & get_instance();

		$sql = "SELECT id
				FROM `{$ci->db->dbprefix('topic')}`
				WHERE hashId='{$hashId}'
				LIMIT 1
			";

		$res = $ci->db->query($sql)->row_array();

		if( $res )
		{	
			return $res['id'];
		}
		else
		{
			return false;
		}
	}
}

/* 
 * --------------------- 笔记 hashId 映射 id  -----------------------------
 */
if( ! function_exists('notepad_hashId_to_nId') )
{
	function notepad_hashId_to_nId( $hashId )
	{
		$ci = & get_instance();

		$sql = "SELECT nId
				FROM `{$ci->db->dbprefix('notepad')}`
				WHERE hashId='{$hashId}'
			";

		$res = $ci->db->query($sql)->row_array();

		if( $res )
		{	
			return $res['nId'];
		}
		else
		{
			return false;
		}
	}
}


if( ! function_exists('real_ip') )
{
	/**
	 * 获得用户的真实IP地址 ecshop
	 *
	 * @access  public
	 * @return  string
	 */
	function real_ip()
	{
	    static $realip = NULL;

	    if ($realip !== NULL)
	    {
	        return $realip;
	    }

	    if (isset($_SERVER))
	    {
	        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        {
	            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

	            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
	            foreach ($arr AS $ip)
	            {
	                $ip = trim($ip);

	                if ($ip != 'unknown')
	                {
	                    $realip = $ip;

	                    break;
	                }
	            }
	        }
	        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
	        {
	            $realip = $_SERVER['HTTP_CLIENT_IP'];
	        }
	        else
	        {
	            if (isset($_SERVER['REMOTE_ADDR']))
	            {
	                $realip = $_SERVER['REMOTE_ADDR'];
	            }
	            else
	            {
	                $realip = '0.0.0.0';
	            }
	        }
	    }
	    else
	    {
	        if (getenv('HTTP_X_FORWARDED_FOR'))
	        {
	            $realip = getenv('HTTP_X_FORWARDED_FOR');
	        }
	        elseif (getenv('HTTP_CLIENT_IP'))
	        {
	            $realip = getenv('HTTP_CLIENT_IP');
	        }
	        else
	        {
	            $realip = getenv('REMOTE_ADDR');
	        }
	    }

	    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

	    return $realip;
	}

}

/* 遞規算法 */
if ( ! function_exists('website_item'))
{
	function website_item( $data, $sign = 0 )
	{
		$ci = & get_instance();

		$str = '';
		
		foreach($data as $k => $v)
		{
			$str .= $ci->load->view('xcspace/content/websiteViews.php', $v, true);
		}
		
		return $str;
	}
}

/*
*@ access: 分頁
*@ param: $dir 文件夾路徑   $sort  是否反向排序
*@ return: Array, 返回data.php內組成的數組
*/
if ( ! function_exists('pagination'))
{
	function pagination($setting)
	{
		$ci = & get_instance();
		$ci->load->library('pagination');
		/* base */
		$config['base_url'] = $setting['base_url'];
		$config['total_rows'] = $setting['total_rows'];
		$config['per_page'] = $setting['per_page'];
		$config['uri_segment'] = $setting['uri_segment'];
		$config['use_page_numbers'] = TRUE; //显示的是当前页码
		// $config['full_tag_opern'] = '<li>';
		// $config['full_tag_close'] = '</li>'; //说明使用什么标签包围分页链接
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = '>>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '<<';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$ci->pagination->initialize($config);
		return $ci->pagination->create_links();
	}
}

/*
*@ access: 分頁
*@ param: $dir 文件夾路徑   $sort  是否反向排序
*@ return: Array, 返回data.php內組成的數組
*/
if ( ! function_exists('pagination2'))
{
	function pagination2($setting)
	{
		$ci = & get_instance();
		$ci->load->library('pagination');
		/* base */
		$config['base_url'] = $setting['base_url'];
		$config['total_rows'] = $setting['total_rows'];
		$config['per_page'] = $setting['per_page'];
		$config['uri_segment'] = $setting['uri_segment'];
		$config['use_page_numbers'] = TRUE; //显示的是当前页码
		// $config['full_tag_opern'] = '<a>';
		// $config['full_tag_close'] = '</a>'; //说明使用什么标签包围分页链接
		$config['first_tag_open'] = '<a>';
		$config['first_tag_close'] = '</a>';
		$config['first_link'] = 'First';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<a>';
		$config['last_tag_close'] = '</a>';
		$config['num_tag_open'] = '<a>';
		$config['num_tag_close'] = '</a>';
		$config['cur_tag_open'] = '<span class="cur_tag"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></span>';
		$config['next_link'] = '>>';
		$config['next_tag_open'] = '<a>';
		$config['next_tag_close'] = '</a>';
		$config['prev_link'] = '<<';
		$config['prev_tag_open'] = '<a>';
		$config['prev_tag_close'] = '</a>';
		
		$ci->pagination->initialize($config);
		return $ci->pagination->create_links();
	}
}

/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */