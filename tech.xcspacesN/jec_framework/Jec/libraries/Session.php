<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * Jec异常、错误处理类
 */

class Session
{
    /**
     * @static
     * 初始化Session句柄
     * @return bool
     */
    public static function init()
    {
        global $CONFIG;

        if(!isset($CONFIG['sess_cache_driver']) || !$CONFIG['sess_cache_driver'])
            return false;

        $className = 'Session_'.$CONFIG['sess_cache_driver'];
        
        $ret = session_set_save_handler(
            array($className, "_open")
            , array($className, "_close")
            , array($className, "_read")
            , array($className, '_write')
            , array($className, "_destroy")
            , array($className, "_gc")
        );
        //还原session_start造成阅读器 后退按钮所有区域内容为空的bug
        session_cache_limiter('private, must-revalidate');
        return $ret;
    }

    /**
     * @static
     * 开始session,它间接调用session_start()
     */
    public static function start()
    {
        session_start();
    }

    /**
     * @static
     * 销毁session,它间接调用session_destroy()
     */
    public static function destroy()
    {
        session_destroy();
    }

}
