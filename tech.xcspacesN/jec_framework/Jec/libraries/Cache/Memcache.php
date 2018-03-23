<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * Memcache缓存操作驱动类
 */

class Cache_Memcache extends Memcache
{
    private $_expire = 0;

    public function __construct()
    {
        global $CONFIG;
        $cfg = $CONFIG['memcache'];
        
        $this->_expire = (int)$CONFIG['cache_expire'];
        $this->connect($cfg['host'], $cfg['port']);
    }

    public function set($key, $value, $timeout=null)
    {
        if($timeout === null)
            $timeout = $this->_expire;
        
        return parent::set($key, $value, 0, $timeout);
    }
}