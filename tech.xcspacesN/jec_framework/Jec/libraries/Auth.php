<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 授权访问认证
 */
class Auth
{
    /**
     * @static
     * 告诉浏览器弹出一个要求授权登录的窗口
     * @param string $msg 提醒消息
     */
    public static function showAuth($msg='Welcome to Jec SYSTEM.')
    {
        $msg = preg_replace('/[^\x20-\x7e]/i', '', $msg);
        header('WWW-Authenticate: Basic realm="' . $msg . '"');
        header('HTTP/1.0 401 Unauthorized');
        //@header('HTTP/1.1 404 Not Found');
        //@header('Status: 404 Not Found');
        if (php_sapi_name() !== 'cgi-fcgi')header('status: 401 Unauthorized');

        exit;
    }

    /**
     * 获取用户提交的用户名
     * @return string
     */
    public static function getUsername()
    {
        return _getEnv('PHP_AUTH_USER');
    }

    /**
     * 获取用户提交的密码
     * @return string
     */
    public static function getPassword()
    {
        return _getEnv('PHP_AUTH_PW');
    }

}