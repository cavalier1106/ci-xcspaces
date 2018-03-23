<?php
/**
 * @copyright: @XCSPACES
 * @description: redis操作类
 * @file: lib_redis.php
 * @author: Quan Zelin
 * @charset: UTF-8
 * @time: 2014-12-11  16:30
 * @version 1.0
 **/

class Lib_redis {

	// private $redis = NULL;
	private $_CI;
	private $redis_conf;

	/**
	 * @param string $host
	 * @param int $post
	 */
	public function __construct( $config = NULL ) 
	{
		$this->_CI = & get_instance();
		$this->redis_conf = $this->_CI->config->item('redis');

		if( $config == NULL )
		{
			$config = $this->redis_conf['xcspace'];
		}

		$host = isset( $config['host'] ) ? $config['host'] : '127.0.0.1';
		$port = isset( $config['port'] ) ? $config['port'] : 6379;
		$auth = isset( $config['auth'] ) ? $config['auth'] : '';
		
		// if( !empty( $auth ) ){
		// 	$this->_CI->redis = new Redis();
		// 	if( !$this->_CI->redis->connect($host, $port) || !$this->_CI->redis->auth( $auth ) )
		// 		$this->_CI->redis = NULL;
		// }

		// if( $this->_CI->redis == NULL )
		// {
			$this->_CI->redis = new Redis();
			if( !$this->_CI->redis->connect( $host, $port ) )
				$this->_CI->redis = NULL;
		// }

		return $this->_CI->redis;
	}
	
	public function is_NULL()
	{
		return is_NULL( $this->_CI->redis );
	}
	
	public function close()
	{
		$this->_CI->redis->close();
	}
	
	public function auth( $pwd )
	{
		return $this->_CI->redis->auth( $pwd );
	}
	
	/**
	 * 设置值  构建一个字符串
	 * @param string $key KEY名称
	 * @param string $value  设置值
	 * @param int $timeOut 时间  0表示无过期时间
	 */
	public function set( $key, $value, $timeOut = 0 ) 
	{
		$retRes = $this->_CI->redis->set( $key, $value );
		if ($timeOut > 0)
			$this->_CI->redis->expire('{$key}', $timeOut);
		return $retRes;
	}

	/**
	 * 构建一个集合(无序集合)
	 * @param string $key 集合Y名称
	 * @param string|array $value  值
	 **/
	public function sadd( $key, $value )
	{
		return $this->_CI->redis->sadd( $key, $value );
	}

	/**
	 * 构建一个集合(有序集合)
	 * @param string $key 集合名称
	 * @param string|array $value  值
	 **/
	public function zadd( $key, $value )
	{
		return $this->_CI->redis->zadd( $key, $value );
	}

	/**
	 * 取集合对应元素
	 * @param string $setName 集合名字
	 **/
	public function smembers( $setName )
	{
		return $this->_CI->redis->smembers( $setName );
	}

	/**
	 * 构建一个列表(先进后去，类似栈)
	 * @param sting $key KEY名称
	 * @param string $value 值
	 **/
	public function lpush( $key, $value )
	{
		return $this->_CI->redis->lpush( $key, $value );
	}

	/**
	 * 构建一个列表(先进先去，类似队列)
	 * @param sting $key KEY名称
	 * @param string $value 值
	 **/
	public function rpush( $key, $value )
	{
		return $this->_CI->redis->rpush( $key, $value );
	}
	
	/**
	 * 获取所有列表数据（从头到尾取）
	 * @param sting $key KEY名称
	 * @param int $head  开始
	 * @param int $tail     结束
	 **/
	public function lranges( $key, $head, $tail)
	{
		return $this->_CI->redis->lrange( $key, $head, $tail );
	}

	/**
	 * HASH类型
	 * @param string $tableName  表名字key
	 * @param string $key        字段名字
	 * @param sting $value       值
	 **/
	public function hset( $tableName, $field, $value )
	{
		return $this->_CI->redis->hset( $tableName, $field, $value );
	}
	
	/**
	 * HASH类型
	 * @param string $tableName  表名字key
	 * @param string $field      字段名字
	 **/
	public function hget( $tableName, $field )
	{
		return $this->_CI->redis->hget( $tableName, $field );
	}
	
	/**
	 * HASH类型
	 * @param string $tableName  表名字key
	 **/
	public function hgetall( $tableName )
	{
		return $this->_CI->redis->hgetall( $tableName );
	}

	/**
	 * HASH类型
	 * @param string $tableName  表名字key
	 * @param string $field      字段名字
	 **/
	public function hdel( $tableName, $field )
	{
		return $this->_CI->redis->hdel( $tableName, $field );
	}


	/**
	 * 设置多个值
	 * @param array $keyArray KEY名称
	 * @param string|array $value 获取得到的数据
	 * @param int $timeOut 时间
	 **/
	public function sets( $keyArray, $timeout ) 
	{
		if ( is_array( $keyArray ) ) 
		{
			$retRes = $this->_CI->redis->mset( $keyArray );
			if ($timeout > 0) 
			{
				foreach ($keyArray as $key => $value) {
					$this->_CI->redis->expire( $key, $timeout );
				}
			}
			return $retRes;
		}
		else 
		{
			return FALSE;
		}
	}

	/**
	 * 通过key获取数据
	 * @param string $key KEY名称
	 **/
	public function get( $key ) 
	{
		$result = $this->_CI->redis->get( $key );
		return $result;
	}
	
	/**
	 * 同时获取多个值
	 * @param ayyay $keyArray 获key数值
	 **/
	public function mget($keyArray) 
	{
		if ( is_array( $keyArray ) ) {
			return $this->_CI->redis->mget( $keyArray );
		} else {
	        return FALSE;
		}
	}
	
	/**
	 * 获取所有key名，不是值
	 **/
	public function keys( $key ) 
	{
		return $this->_CI->redis->keys( $key );
	}
	
	/**
	 * 删除一条数据key
	 * @param string $key 删除KEY的名称
	 **/
	public function del( $key ) 
	{
		return $this->_CI->redis->delete( $key );
	}
	
	/**
	* 同时删除多个key数据
	* @param array $keyArray KEY集合
	*/
	public function dels( $keyArray ) 
	{
		if ( is_array( $keyArray ) ) 
		{
			return $this->_CI->redis->del( $keyArray );
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 数据自增
	 * @param string $key KEY名称
	 **/
	public function incr( $key ) 
	{
		return $this->_CI->redis->incr( $key );
	}
	
	public function incrby( $key, $num )
	{
		return $this->_CI->redis->incrby( $key, (int)$num );
	}

	public function hincrby( $key ,$member, $num )
	{
		return $this->_CI->redis->hIncrBy( $key ,$member, (int)$num);
	}

	/**
	 * 数据自减
	 * @param string $key KEY名称
	 **/
	public function decrement( $key ) 
	{
		return $this->_CI->redis->decr( $key );
	}
		 

	/**
	 * 判断key是否存在
	 * @param string $key KEY名称
	 **/
	public function exists( $key )
	{
		return $this->_CI->redis->exists( $key );
	}
	
	/**
	 * 重命名- 当且仅当newkey不存在时，将key改为newkey ，当newkey存在时候会报错哦RENAME
	 *  和 rename不一样，它是直接更新（存在的值也会直接更新）
	 * @param string $Key KEY名称
	 * @param string $newKey 新key名称
	 **/
	public function updateName( $key, $newKey )
	{
		return $this->_CI->redis->renamenx( $key, $newKey );
	}
	
    /**
     * 获取KEY存储的值类型
     * none(key不存在) int(0)  string(字符串) int(1)   list(列表) int(3)  set(集合) int(2)   zset(有序集) int(4)    hash(哈希表) int(5)
	 * @param string $key KEY名称
	 **/
	public function dataType( $key )
	{
		return $this->_CI->redis->type( $key );
	}
	 
	/**
	 * 清空数据
	 **/
	public function flushAll() 
	{
		return $this->_CI->redis->flushAll();
	}
	
	/**
	 * 返回redis对象
	 * redis有非常多的操作方法，只封装了一部分
	 * 拿着这个对象就可以直接调用redis自身方法
	 * eg:$redis->redisOtherMethods()->keys('*a*')   keys方法没封
	 **/
	 public function redisOtherMethods() 
	 {
	 	return $this->_CI->redis;
	 }
}