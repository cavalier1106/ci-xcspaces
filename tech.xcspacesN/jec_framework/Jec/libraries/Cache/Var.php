<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 使用变量高速缓存处理类
 */
class Cache_Var
{
    /**
     * 静态访问变量，保存全局缓存内容
     * @var array
     */
    private $global_cache = array(); // array('key','value')
    
    public function __destruct()
    {
        $this->global_cache = array();
    }
    
    public function get($key)
    {
        return isset($this->global_cache[$key]) ? $this->global_cache[$key] : false;
    }
    
    public function set($key, $val)
    {
        $this->global_cache[$key] = $val;
        return true;
    }
    
    public function delete($key)
    {
        unset($this->global_cache[$key]);
        return true;
    }
    
    public function flush()
    {
        $this->global_cache = array();
        return true;
    }
}