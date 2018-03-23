<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 保存会话到数据库
 */
class Session_DB
{
    private static $_lifeTime = 0;
    
    public static function _open($savePath, $sessName)
    {
        global $CONFIG;
        self::$_lifeTime = function_exists('get_cfg_var') ? get_cfg_var("session.gc_maxlifetime") : $CONFIG['sess_timeout'];
        
        return true;
    }
    
    public static function _close()
    {
        return true;
    }
    
    public static function _read($sessID)
    {
        $result = DB::getInstance()->getRow("SELECT session_value FROM @#@_sessions WHERE session_id = '{$sessID}' AND session_expires > " . time());
        
        return $result['session_value'];
    }
    
    public static function _write($sessID, $sessData)
    {
        $newExp = time() + self::$_lifeTime;

        $status = DB::getInstance()->query("REPLACE INTO @#@_sessions ( session_id, session_expires, session_value) VALUES( '{$sessID}', '{$newExp}', '{$sessData}')");

        return (bool)$status;
    }
    
    public static function _destroy($sessID)
    {
        $status = DB::getInstance()->query("DELETE FROM @#@_sessions WHERE session_id = '{$sessID}'");
        
        return (bool)$status;
    }
    
    public static function _gc($sessMaxLifeTime)
    {
        $status = DB::getInstance()->query("DELETE FROM @#@_sessions WHERE session_expires < " . time());
        //DB::getInstance()->query('OPTIMIZE TABLE `@#@_sessions`');
        return (bool)$status;
    }
}