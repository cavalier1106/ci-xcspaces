<?php
/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 * Jec异常、错误处理类
 */

class JecException extends Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
        //这里不能直接调用doError做任何操作,因为throw后如果没有try,catch会调用doException方法的,否则try语句会失效
        //self::doError($code, $message, '', 0, array(), false);
    }

    /**
     * 处理错误
     * @param string $string 错误信息
     * @param int $code 错误代码
     * @param string $file 错误文件名
     * @param int $line 所在行数
     * @param array $context 错误内容
     * @param bool $exit 是否退出
     * @return null
     */
    public static function doError($code, $string, $file, $line, $context, $exit=true)
    {
        //注意不要使用$context，因为它可能是某个函数的参数数组，如strtoarray的array($string=>'', $lineChar=>",", $spChar=>":")
        $contextArr = debug_backtrace();
        unset($contextArr[0]); //不显示本身#0 JecException::doError(#5) called at [:]
        return self::showError("Error: ".$string, $code, $file, $line, $contextArr, false, $exit);
    }

    public static function onShutdown()
    {
        //E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, and most of E_STRICT
        $error = error_get_last();
        if(!$error)
            return;
        ob_end_clean();
        self::showError("FatalError: ".$error['message'], $error['type'], $error['file'], $error['line'], null);
        exit;
    }

    /**
     * @static
     * 处理异常信息
     * @param Exception $e
     * @return null
     */
    public static function doException($e)
    {
        $trace = $e->getTrace();
        return self::showError("Exception: ".$e->message, $e->code, $e->file, $e->line, $trace);
    }

    /**
     * 显示错误
     * @param string $string 错误信息
     * @param int $code 错误代码
     * @param string $file 错误文件名
     * @param int $line 所在行数
     * @param array $contextArr 错误内容
     * @param bool $show 非调试状态也显示
     * @param bool $exit 是否退出当前程序
     * @return null
     */
    public static function showError($string, $code, $file, $line, $contextArr, $show=false, $exit=true)
    {
        global $CONFIG;
        //Undefined index
        if ($code == E_NOTICE || $code == E_USER_NOTICE)
            return;

        $strArr = array();
        $contextHtml = $contextCli = '';

        if (is_array($contextArr)) {
            $len = count($contextArr);
            foreach ($contextArr as $i => $call)
            {
                $_file = self::filter_file($call['file']);
                $_argc = count($call['args']);

                $object = '';
                if (isset($call['class'])) {
                    $object = $call['class'].$call['type'];
                }
                $context = '#'.str_pad($i, $len, ' ').$object.$call['function'].'(#'.$_argc.') called at [';
                $contextHtml .= $context.$_file.':'.$call['line']."]<br/>";
                $contextCli .= $context.$call['file'].':'.$call['line']."]\n";
            }
        }

        $error_level = defined('ERROR_LEVEL') ? ERROR_LEVEL : ERROR_ALL;
        if ($error_level == ERROR_TO_FILE || $error_level == ERROR_NONE)
            $isLogtofile = true;
        else
            $isLogtofile = false;

        $strArr[] = "#{$string}";
        if ($file)
            $strArr[] = "File: {$file}:{$line}";

        if ($contextCli)
            $strArr[] = "Context:\n{$contextCli}";

        if ($isLogtofile)
            self::log(implode("\n", $strArr));

        $key = date('Y-m-d H:i:s').'/'.crc32($string);

        if(!isCLI())
        {
            $strArr = array();

            //处理一下，不然在浏览器看<, &amp;这些字符时会有问题
            $string = htmlspecialchars($string);

            $charset = $CONFIG['html_charset'] ? $CONFIG['html_charset'] : 'utf-8';
            $strArr[] = '<meta http-equiv="Content-Type" content="text/html;charset=' . $charset . '"/>';
            $strArr[] = '<div style="position:absolute;left:3%;top:3%;right:3%;text-align:left;border:2px solid #F00;background:#FF9;padding:10px; font-family:fixedsys,mono; font-size:16px;">';
            $strArr[] = "Time: " . $key . '<br/>';
            $strArr[] = '<pre style="word-break:break-all;word-wrap:break-word;">';
            $strArr[] = "#" . $string;
            $strArr[] = '</pre>';
            if ($file){
                $file = self::filter_file($file);
                $strArr[] = "File: {$file}:{$line}<br/>";
            }

            if ($contextHtml){
                $strArr[] = "Context: <br/>";
                $strArr[] = $contextHtml;
            }
            $strArr[] = '</div>';
        }

        if ($show || $error_level != ERROR_NONE)
            echo implode("\n", $strArr);
        else
            echo '<h5>发生了一个错误，请将错误ID：['.$key.']报告给管理员</h5>';

        if ($exit)
            exit;
    }

    private static function filter_file($file)
    {
        return substr($file, strrpos($file,'/'));
    }

    /**
     * 将错误信息写入日志文件
     * @param string $str
     * @return bool
     */
    public static function log($str)
    {
        $subfix = isCLI() ? 'cli' : 'web';
        $file = VAR_PATH . '/log/i.error.'.$subfix.'.html';
        return file_put_contents($file, getDateStr().str_repeat('-',15)."\n".$str."\n\n", FILE_APPEND);
    }

}//end class