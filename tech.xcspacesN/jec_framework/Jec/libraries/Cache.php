<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 *
 * 缓存处理类
 */

class Cache
{
    private static $_drv = array();
    
    /**
     * 获取一个实例
     * @param string $drive 缓存类型，如File, Memcache
     * @return Cache_File 或其它$CONFIG配置的缓存驱动类型
     */
    public static function getInstance($drive='')
    {
        global $CONFIG;

        if(!$drive)
        {
            $drive = $CONFIG['cache_driver'];
        }

        if(isset(self::$_drv[$drive]))
            return self::$_drv[$drive];

        if(!$CONFIG['cache_expire'])
            $CONFIG['cache_expire'] = 360 * 24 * 3600;

        $drvName = 'Cache_' . ucfirst(strtolower($drive));

        self::$_drv[$drive] = new $drvName();

        return self::$_drv[$drive];
    }

}