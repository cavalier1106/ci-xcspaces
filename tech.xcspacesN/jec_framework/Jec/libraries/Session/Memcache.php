<?php
/**
 *@copyright Jec
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 */
class Session_Memcache
{
    private static $_lifeTime = 0;
    private static $_mem = null;

    public static function _open()
    {
        if (!extension_loaded('memcache'))
            throw new JecException('没有找到Memcache环境，无法处理Session!');
        
        global $CONFIG;
        $cfg = $CONFIG['memcache'];
        
        self::$_lifeTime = function_exists('get_cfg_var') ? get_cfg_var("session.gc_maxlifetime") : $CONFIG['sess_timeout'];
        self::$_mem      = new Memcache;
        self::$_mem->connect($cfg['host'], $cfg['port']);
        return true;
    }
    
    public static function _close()
    {
        return true;
    }
    
    public static function _read($sessID)
    {
        return self::$_mem->get($sessID);
    }
    
    public static function _write($sessID, $sessData)
    {
        return self::$_mem->set($sessID, $sessData, 0, self::$_lifeTime);
    }
    
    public static function _destroy($sessID)
    {
        return self::$_mem->delete($sessID);
    }
    
    public static function _gc()
    {
        return true;
    }
    
}
