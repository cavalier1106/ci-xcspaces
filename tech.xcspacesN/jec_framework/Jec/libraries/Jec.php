<?php
/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 *
 * Jec基础类
 */
class Jec
{
    public static function has($index)
    {
        return isset($_POST[$index]) || isset($_GET[$index]);
    }

    /**
     * @static
     * 获取一个正数
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return int
     */
    public static function getInt($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return (int)$value;
    }

    /**
     * @static
     * 获取一个正数
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return array(@type int)
     */
    public static function getIntArray($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return !is_array($value) || !$value ? array() : parseInt($value);
    }

    /**
     * 取得请求为浮数值
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return float
     */
    public static function getFloat($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return (float)$value;
    }

    /**
     * 取得请求为浮数值
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return array (@type float)
     */
    public static function getFloatArray($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return !is_array($value) || !$value ? array() : parseFloat($value);
    }

    /**
     * 返回一个Unix时间戳
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return int
     */
    public static function getTime($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return self::_getTime($value);
    }

    private static function _getTime($value)
    {
        if(!$value || !is_string($value))return 0;
        //phpdoc: 成功则返回时间戳，否则返回 FALSE。在 PHP 5.1.0 之前本函数在失败时返回 -1
        $value = strtotime($value);
        if ($value < 1)
            return 0;
        return $value;
    }

    /**
     * 获取一个标准的日期描述
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @param string $format 和date函数的format参数一致
     * @return string 返回空字符串或2012-01-01 23:23:23这样的格式
     */
    public static function getDate($index, $method = '', $format='Y-m-d H:i:s')
    {
        $ts = self::getTime($index, $method);
        if($ts == 0)
            return '';

        return getDateStr($ts, $format);
    }

    /**
     * 获取一个经过HTML过滤和转义的GPC变量
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return string 返回将返回false,否则则可能是字符串或数组
     */
    public static function getVar($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        return self::_getVar((string)$value);
    }

    /**
     * 获取一个经过HTML过滤和转义的GPC变量
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return array 返回将返回false,否则则可能是字符串或数组
     */
    public static function getVarArray($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        $value = self::_getVar($value);
        if(!$value || !is_array($value))
            return array();
        return $value;
    }

    private static function _getVar($value)
    {
        if(!$value)return $value;
        $value = _htmlspecialchars($value);
        $value = _addslashes($value);
        return _trim($value);
    }

    /**
     * 获取一个数组请求，如果index请求值的键不在map的键中则抛出一个错误
     * @static
     * @param string $index GPC请求变量键名
     * @param array $map 字段数组，格式：array(key=>type,,)，如: array('name'=>'var'),
     *             type类型有（区分大小写）:int,int[],float,float[],date,time,keyword,var,var[]；默认为var
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return array
     * @throws JecException
     */
    public static function getMap($index, $map, $method = '')
    {
        $value = self::_getGPC($index, $method);
        if($value===false)
            return array();
        if(!is_array($value))
            throw new JecException('非数组请求：'.$index);

        $newValue = array();

        foreach($map as $name => $type)
        {
            $Pv = isset($value[$name]) ? $value[$name] : null;
            switch($type)
            {
                case 'int':
                    $Pv = (int)$Pv;
                    break;
                case 'int[]':
                    $Pv = is_array($Pv) && $Pv ? parseInt($Pv) : array();
                    break;
                case 'float':
                    $Pv = (float)$Pv;
                    break;
                case 'float[]':
                    $Pv = is_array($Pv) && $Pv ? parseFloat($Pv) : array();
                    break;
                case "date" :
                    $Pv = getDateStr(self::_getTime($Pv));
                    break;
                case 'time':
                    $Pv = self::_getTime($Pv);
                    break;
                case 'keyword':
                    $Pv = preg_replace('/[^\w\-]/', '', $Pv);
                    break;
                case 'var[]':
                    $Pv = self::_getVar($Pv);
                    if(!is_array($Pv) || !$Pv)$Pv=array();
                    break;
                case 'var':
                default:
                    $Pv = (string)self::_getVar($Pv);
            }
            $newValue[$name] = $Pv;
        }
        unset($value);

        return $newValue;
    }

    /**
     * 获取一个经过HTML及特殊字符过滤和转义后的请求变量，一般用来过滤搜索关键字
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return bool|string|array 失败将返回false
     */
    public static function getKeyword($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        if(!$value)return '';
        $value = preg_replace('/[^\w\-]/', '', (string)$value);
        return $value;
    }

    /**
     * 获取一个经过转义后的请求变量
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return string
     */
    public static function getString($index, $method = '')
    {
        $value = (string)self::_getGPC($index, $method);
        if(!$value)return '';
        $value = addslashes($value);
        return trim($value);
    }

    /**
     * 获取一个经过转义后的请求变量
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return array(@type string)
     */
    public static function getStringArray($index, $method = '')
    {
        $value = self::_getGPC($index, $method);
        if(!$value || !is_array($value))return array();
        $value = _addslashes($value);
        return _trim($value);
    }

    /**
     * 获取一个经过HTML安全过滤和转义后的请求变量
     * @param string $index GPC请求变量键名
     * @param array $allowed_elements 允许的标签列表, 格式: array('h2'=>true,'span'=>true)
     * @param array $allowed_attributes 允许的属性列表, 格式: array('h2.class' => true, 'span.id' => true)
     * @param array $allowed_protocols 允许的协议, 格式: array('http', 'https', 'ftp', 'mailto')
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return string
     */
    public static function getXhtml($index, $allowed_elements=array(), $allowed_attributes=array(), $allowed_protocols=array('http', 'https', 'ftp', 'mailto'), $method = '')
    {
        $value = (string)self::_getGPC($index, $method);
        if(!$value)return $value;
        //去掉自动转换不然kses不正常
        if (MAGIC_QUOTES_GPC)
            $value = stripslashes($value);
        loadPlugins('JecHTMLPurifier');
        $value = JecHTMLPurifier::filter($value, $allowed_elements, $allowed_attributes, $allowed_protocols);
        $value = _addslashes($value, 1);
        return $value;
    }

    /**
     * 从GPC数组中寻找索引值
     * @param string $index GPC请求变量键名
     * @param string $method 请求方式限制,值为: GET,POST,COOKIE
     * @return bool|string|array
     */
    private static function _getGPC($index, $method = '')
    {
        if ($method) {
            $name = '_' . $method;
            if (!isset($GLOBALS[$name][$index]))
                return false;
            return $GLOBALS[$name][$index];
        }
        if (isset($_POST[$index]))
            return $_POST[$index];
        if (isset($_GET[$index]))
            return $_GET[$index];

        return false;
    }
}