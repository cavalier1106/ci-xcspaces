<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ------------------------------------------------------------------
 * ------------------- 分表->主题点击次数详细信息表 -----------------
 * ------------------------------------------------------------------
 */
if( ! function_exists('create_topic_clicks_details_table') )
{
	function create_topic_clicks_details_table( $hash, $ENGINE = 'InnoDB', $pre = 'xcspace_' )
	{
		$ci = & get_instance();

		$ci->wdb = $ci->load->database('xcs_topic_clicks_detail_write', true);
		
		$sql = "CREATE TABLE If Not Exists `{$pre}topic_clicks_details_{$hash}` (
				  `tid` int(11) NOT NULL COMMENT '主题ID',
				  `uname` varchar(100) NOT NULL DEFAULT '' COMMENT '点击用户',
				  `random` varchar(8) NOT NULL DEFAULT '' COMMENT '随机码',
				  `view_ip` char(16) DEFAULT NULL COMMENT '访问IP',
				  `time` int(11) NOT NULL DEFAULT '0' COMMENT '主题点击时间',
				  PRIMARY KEY (`tid`,`time`,`random`),
				  KEY `tid` (`tid`) USING BTREE
				) ENGINE={$ENGINE} DEFAULT CHARSET=utf8 COMMENT='主题点击次数详细信息表';
			";

		$data = $ci->wdb->query($sql);

		return $data;
	}
}


/* ------------------------------------------------------------------
 * --------------------- 分表->主题回复详细信息表 -------------------
 * ------------------------------------------------------------------
 */
if( ! function_exists('create_topic_reply_table') )
{
	function create_topic_reply_table( $hash, $ENGINE = 'InnoDB', $pre = 'xcspace_' )
	{
		$ci = & get_instance();

		// $ci->db = $ci->load->database('xcs_topic_reply_write', true);

		$sql = "CREATE TABLE If Not Exists `{$pre}topic_reply_{$hash}` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `tid` int(11) NOT NULL DEFAULT '0' COMMENT '主题ID',
				  `content` text NOT NULL COMMENT '回复主题内容',
				  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '回复主题审核状态：1通过 2拒绝 3审核中 4删除',
				  `view_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '回复主题审核状态：0未查看 1已查看',
				  `reply_userid` int(11) NOT NULL DEFAULT '0' COMMENT '回复主题用户ID',
				  `reply_username` varchar(30) NOT NULL DEFAULT '' COMMENT '回复主题用户',
				  `thanks_reply_topic_str` text NOT NULL COMMENT '记录话题回复用户是否感谢：uid,uid',
				  `time` int(11) DEFAULT NULL COMMENT '回复主题时间',
				  PRIMARY KEY (`id`),
				  KEY `tid` (`tid`) USING BTREE,
				  KEY `status` (`status`) USING BTREE,
				  KEY `view_status` (`view_status`) USING BTREE,
				  KEY `reply_userid` (`reply_userid`) USING BTREE,
				  KEY `reply_username` (`reply_username`) USING BTREE,
				  KEY `time` (`time`) USING BTREE
				) ENGINE={$ENGINE} DEFAULT CHARSET=utf8 COMMENT='主题回复';
			";

		$data = $ci->db->query($sql);

		return $data;
	}
}

/* ------------------------------------------------------------------
 * ---------------- 分表->用户获取铜币记录详细信息表 ----------------
 * ------------------------------------------------------------------
 */
if( ! function_exists('create_coin_record_table') )
{
	function create_coin_record_table( $hash, $ENGINE = 'InnoDB', $pre = 'xcspace_' )
	{
		$ci = & get_instance();

		// $ci->db = $ci->load->database('xcs_coin_record_write', true);

		$sql = "CREATE TABLE If Not Exists `{$pre}coin_record_{$hash}` (
				  `uid` int(11) NOT NULL,
				  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '铜币',
				  `desc` varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
				  `op_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '操作类型:0+初始资本,1+每日登录奖励,2+每日活跃度奖励,3-创建主题,4-创建回复,5+主题回复收益,6+注册推荐收益,7-发送谢意',
				  `random` varchar(8) NOT NULL DEFAULT '' COMMENT '随机码',
				  `time` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
				  PRIMARY KEY (`uid`,`time`,`random`),
				  KEY `uid` (`uid`) USING BTREE
				) ENGINE={$ENGINE} DEFAULT CHARSET=utf8 COMMENT='用户获取铜币记录';
			";

		$data = $ci->db->query($sql);

		return $data;
	}
}

/* ------------------------------------------------------------------
 * ---------------- 分表->主题回复详细信息表视图 ----------------
 * ------------------------------------------------------------------
 */
if( ! function_exists('get_topic_reply') )
{
	function get_topic_reply( $where = 1, $pre = 'xcspace_', $db = 'xcspace' )
	{
		$ci = & get_instance();

		// $ci->db = $ci->load->database('xcs_topic_reply_write', true);

		$s = "show tables like \"{$pre}topic_reply%\"";
		$topic_reply_tables = $ci->db->query($s)->result_array();

		$tables = array();
		foreach ($topic_reply_tables as $k => $v) 
		{
			$tables[] = $v["Tables_in_{$db} ({$pre}topic_reply%)"];
		}

		$sql = "CREATE OR  REPLACE VIEW VIEW_topic_reply AS ";
		$nums = count( $tables ) - 1;

		if( $tables )
		{
			foreach( $tables as $k => $v )
			{
				$sql .= "(SELECT * FROM {$v} WHERE {$where})";
				if( $nums != $k )
				{
					$sql .= " UNION ";
				}
			}

			$data = $ci->db->query( $sql );

			return 'VIEW_topic_reply';
		}
		else
		{
			return false;
		}
		
	}
}

/* ------------------------------------------------------------------
 * ------------- 分表->用户获取铜币记录详细信息表视图  --------------
 * ------------------------------------------------------------------
 */
if( ! function_exists('create_coin_record') )
{
	function create_coin_record( $where = 1, $pre = 'xcspace_', $db = 'xcspace' )
	{
		$ci = & get_instance();

		// $ci->db = $ci->load->database('xcs_coin_record_write', true);

		$s = "show tables like \"{$pre}coin_record%\"";
		$topic_reply_tables = $ci->db->query($s)->result_array();

		$tables = array();
		foreach ($topic_reply_tables as $k => $v) 
		{
			$tables[] = $v["Tables_in_{$db} ({$pre}coin_record%)"];
		}

		$sql = "CREATE OR  REPLACE VIEW VIEW_coin_record AS ";
		$nums = count( $tables ) - 1;

		foreach( $tables as $k => $v )
		{
			$sql .= "(SELECT * FROM {$v} WHERE {$where})";
			if( $nums != $k )
			{
				$sql .= " UNION ";
			}
		}

		$data = $ci->db->query( $sql );

		return 'VIEW_coin_record';
	}
}

/* -------------------------------------------------------------------
 * -分表->提醒系统【根据相应用户更新表topic_reply字段view_status为1】-
 * -------------------------- 日后修改 -------------------------------
 * -------------------------------------------------------------------
 */
if( ! function_exists('alter_topic_reply') )
{
	function alter_topic_reply( $tid_arr, $pre = 'xcspace_', $db = 'xcspace' )
	{
		$ci = & get_instance();
		
		// $ci->db = $ci->load->database('xcs_topic_reply_write', true);

		$s = "show tables like \"{$pre}topic_reply%\"";
		$topic_reply_tables = $ci->db->query($s)->result_array();

		$tables = array();
		foreach ($topic_reply_tables as $k => $v) 
		{
			$tables[] = $v["Tables_in_{$db} ({$pre}topic_reply%)"];
		}

		$nums = count( $tables ) - 1;

		if( !empty( $tid_arr ) )
		{
			foreach( $tables as $k => $v )
			{
				$sql = "UPDATE {$v} SET view_status=1 WHERE tid IN(" . implode(',', $tid_arr) . ")";
				$ci->db->query( $sql );
			}
		}

		// echo "<pre>"; var_dump($sql); exit;

		return;
	}
}


/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */