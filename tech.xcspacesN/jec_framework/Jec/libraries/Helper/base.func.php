<?php

/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 * 公共函数库
 */

/**
 * 自动加载方法
 * @param string $class 类名
 * @return bool
 */
function Jec_autoload($class)
{
    $class = ucfirst($class);
    if (class_exists($class, false) || interface_exists($class, false))
        return true;
    $file = str_replace('_', DS, $class) . '.php';

    require $file;
    return true;
}

/**
 * 载入一个插件
 * @param $file_basename 插件名称，不包含扩展名.php
 * @return bool|mixed
 */
function loadPlugins($file_basename)
{
    return require_once(PLUGINS_PATH . DS . $file_basename . '.php');
}

/**
 * 调试函数
 * @param $var 要调试的变量
 * @param $exit 是否退出
 * @return null
 */
function d($var, $exit = 1)
{
    $debug = debug_backtrace();
    $dfile = '';
    foreach ($debug as $dval)
    {
        if ($dval['function'] == 'd') {
            $dfile = "{$dval['file']}:{$dval['line']}\n";
        }
    }
    $result = var_export($var, true);//会替换'为\'
    $result = str_replace(array("\\\\","\\'"), array("\\","'"), $result);
    //$result = str_replace("\n", "<br />", $result);
    JecException::showError($dfile . $result, 0, '', 0, null, 1, $exit);
}

/**
 * 是否使用命令行模式执行PHP
 * @return bool
 */
function isCLI()
{
    /**
     * php_sapi_name
     * Returns the interface type, as a lowercase string.
     * Although not exhaustive, the possible return values include aolserver, apache, apache2filter, apache2handler, caudium, cgi (until PHP 5.3), cgi-fcgi, cli, continuity, embed, isapi, litespeed, milter, nsapi, phttpd, pi3web, roxen, thttpd, tux, and webjames.
     **/
    return php_sapi_name() == 'cli';
}

/**
 * 记录调试信息到一个日志文件
 * @param mixed $var 调试变量
 * @param string $logFile 日志文件名
 * @return bool
 */
function _log($var, $logFile = 'iBug.log')
{
    $rt = date('Y/m/d H:i:s') . "\n";
    if (!$var) {
        if (is_array($var))
            $rt = 'array()';
        elseif ($var === false)
            $rt = 'false';
        elseif ($var === null)
            $rt = 'null';
        else
            $rt = var_export($var, true);
    } else
    {
        $rt = var_export($var, true);
    }
    $rt .= "\n\n";
    $file = VAR_PATH . '/log/' . $logFile;

    return file_put_contents($file, $rt, FILE_APPEND);
}

/**
 * 取得一个安全的系统环境变量
 * @param string $key 变量名
 * @return string 环境值
 */
function _getEnv($key)
{
    $ret = '';
    if (isset($_SERVER[$key]) || isset($_ENV[$key]))
        $ret = isset($_SERVER[$key]) ? $_SERVER[$key] : $_ENV[$key];
    switch ($key)
    {
        case 'PHP_SELF':
        case 'PATH_INFO':
        case 'PATH_TRANSLATED':
        case 'HTTP_USER_AGENT':
            $ret = htmlspecialchars($ret, ENT_QUOTES);
            break;
    }
    return getenv($key);
}

/**
 * 获取php.ini配置
 * @param string $varname 变量名
 * @return string
 */
function getCfg($varname)
{
    $result = function_exists('get_cfg_var') ? get_cfg_var($varname) : 0;
    if ($result == 0)
        return 'No';
    elseif ($result == 1)
        return 'Yes';
    else
        return $result;
}

/**
 * 扩展类似JS的alert函数，响应后直接退出php执行脚本
 * @param $msg 提示信息
 * @param $act 默认动作返回上一页，其它：href转到链接，close关闭当前窗口
 * @param $href 网址
 * @return null
 */
function alert($msg = '操作失败 :-(', $act = 'href', $href = '')
{
    global $CONFIG;
    $js = '';
    switch ($act)
    {
        case 'href':
            if (!$href) $href = $_SERVER['HTTP_REFERER'];
            $js = "location.href='$href';";
            break;
        case 'close':
            $js = "window.open('','_parent','');window.close();";
            break;
        default:
            $js = "history.go(-1);";
    }
    echo '
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=' . $CONFIG['html_charset'] . '" /></head>
<body>
<script type="text/javascript">
alert("' . $msg . '");' . $js . '
</script>
</body>
</html>';
    exit();
}

/**
 * 清理空格 - 支持数组
 * @param mixed $var
 * @return mixed
 */
function _trim($var)
{
    if (is_array($var))
        return array_map("_trim", $var);
    return trim($var);
}


/**
 * 合并多个数组为一个数组
 * 注意:本函数不会改变第一个参数数组的键名,其它参数可以为任意类型,如果已经存在相同键名,将做为索引键名处理
 * @param array $firstArray
 * @param ...
 * @return array
 * @throws JecException
 */
function _array_merge($firstArray)
{
    if(!is_array($firstArray))
        throw new JecException('_array_merge第一个参数必须为数组');
    $num = func_num_args();
    for($i=1; $i<$num; $i++)
    {
        $arr = func_get_arg($i);
        if(is_array($arr))
        {
            foreach($arr as $k=>$v)
            {
                if(isset($firstArray[$k]))
                    $firstArray[] = $v;
                else
                    $firstArray[$k] = $v;
            }
        }else{
            $firstArray[] = $arr;
        }
    }
    return $firstArray;
}