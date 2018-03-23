<?php
if(defined('PLUGINS_TEABAG3G_PATH'))
{
    echo '<pre>';
    debug_print_backtrace();
    echo '</pre>';
    exit('重复加载teabag3D');
}
define('PLUGINS_TEABAG3G_PATH', dirname(__FILE__));
require(PLUGINS_TEABAG3G_PATH.'/lib/teabag_3d/classes/teabagFront.php');

class Teabag3D
{

    public static function headerImage($key)
    {
        $captcha = new teabagFace();
        $captcha->setMethod('stream'); //file,raw,stream
        $captcha->setHeight(130);
        $captcha->setWidth(330);
        $captcha->generate();
        $_SESSION[$key] = $captcha->getCode();
        ob_start();
        print_r($_REQUEST);
        debug_print_backtrace();
        $text = "\n\n===================\n".ob_get_contents();
        ob_end_clean();
        file_put_contents('/tmp/test.txt', $text, FILE_APPEND);
    }

    public static function getCode($key)
    {
        return $_SESSION[$key];
    }
}