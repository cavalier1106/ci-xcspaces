<?php
/**
 *@copyright Jec
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 */
class Session_Redis
{
    private static $_lifeTime = 0;
    private static $_redis = null;

    public static function _open()
    {
        if (!extension_loaded('redis'))
            throw new JecException('没有找到redis环境，无法处理Session!');
        
        global $CONFIG;
        $cfg = $CONFIG['redis'];
        
        self::$_lifeTime = function_exists('get_cfg_var') ? get_cfg_var("session.gc_maxlifetime") : $CONFIG['sess_timeout'];
        self::$_redis      = new redis();
        self::$_redis->connect($cfg['host'], $cfg['port']);
        return true;
    }
    
    public static function _close()
    {
        return true;
    }
    
    public static function _read($sessID)
    {
        return self::$_redis->get($sessID);
    }
    
    public static function _write($sessID, $sessData)
    {
        self::$_redis->set($sessID, $sessData);
        return self::$_redis->setTimeout($sessID, self::$_lifeTime);
    }
    
    public static function _destroy($sessID)
    {
        return self::$_redis->delete($sessID);
    }
    
    public static function _gc()
    {
        return true;
    }
    
}
