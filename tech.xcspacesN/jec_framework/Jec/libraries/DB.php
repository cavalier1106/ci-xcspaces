<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 数据库连接类
 * 可以通过new iDB_DRIVER($db);取得第二个数据库连接
 */
class DB
{
    //驱动类
    private static $_drv = array();

    private static $src  = array('%',  "\t",  "'",   '"',   "\\",  "\r",  "\n",  "\x0");
    private static $dest = array("%%", '%09', '%27', '%22', '%5C', '%0D', '%0A', '%00');

    /**
     * 获取一个数据库连接实例
     * @param array $custom_config 其它非默认数据库配置数组
     * @return DB_Mysql 或其它数据库操作类
     */
    public static function getInstance($custom_config=array())
    {
        global $CONFIG;

        if($custom_config)
        {
            $cfg = $custom_config;
        }else{
            $cfg = $CONFIG['db'];
        }
        $key = $cfg['host'].$cfg['db_name'];
        if (self::$_drv[$key])
            return self::$_drv[$key];

        //iDB_Mysql
        $drvName = 'DB_' . ucfirst(strtolower($cfg['type']));
        self::$_drv[$key] = new $drvName($cfg);
        return self::$_drv[$key];
    }

    public static function encode($text, $auto_stripslashes=true)
    {
        if(!is_string($text))
            return $text;
        //不判断MAGIC_QUOTES_GPC，因为php5.4不存在自动转义GPC, Jec::getXX会加转义
        if($auto_stripslashes)
            $text = stripslashes($text);
        //The exact characters that are escaped by this function are the null byte (0), newline (\n), carriage return (\r), backslash (\), single quote ('), double quote (") and substiture (SUB, or \032).
        return str_replace(
            self::$src,
            self::$dest,
          $text);
    }

    public static function decode($text)
    {
        if(!is_string($text))
            return $text;
        return str_replace(
            self::$dest,
            self::$src,
          $text);
    }
}
