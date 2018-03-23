<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * Redis缓存操作驱动类
 */

class Cache_Redis extends Redis
{
    private $_expire = 0;

    public function __construct()
    {
        global $CONFIG;
        $cfg = $CONFIG['redis'];
        
        $this->_expire = (int)$CONFIG['cache_expire'];
        $this->connect($cfg['host'], $cfg['port']);
    }
    
    /**
     * 重载set方法
     * @param string $key
     * @param mixed $value
     * @param null|int $timeout
     * @return bool
     */
    public function set($key, $value, $timeout=null)
    {
        global $CONFIG;
        if($timeout === null)
            $timeout = $this->_expire;
        
        parent::set($key, $value);
        $this->setTimeout($key, $timeout);
        return true;
    }
}