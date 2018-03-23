<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 文件缓存操作驱动类
 */

class Cache_File
{
    private $_expire = 0;
    
    public function __construct()
    {
        global $CONFIG;
        $this -> _expire = $CONFIG['cache_expire'];
    }
    
    private function _getCacheFile($key)
    {
        return VAR_PATH . '/cache/' . md5($key) . '.php';
    }
    
    public function get($key)
    {
        $cache_file = $this->_getCacheFile($key);
        
        //clearstatcache($cache_file);
        
        if (! is_file($cache_file))
            return false;
        
        $cache = unserialize(file_get_contents($cache_file));
        if($cache['expire'] < TIME)
        {
            return false;
        }
        return $cache['data'];
    }

    public function set($key, $val, $timeout = 0)
    {
        if(!$timeout)
            $timeout = $this -> _expire;

        return file_put_contents($this -> _getCacheFile($key), serialize(array('expire'=>TIME+$timeout,'data'=>$val)));
    }
    
    public function delete($key)
    {
        return @unlink($this->_getCacheFile($key));
    }

    public function flush()
    {
        _rmdir(VAR_PATH . '/cache');
    }
}