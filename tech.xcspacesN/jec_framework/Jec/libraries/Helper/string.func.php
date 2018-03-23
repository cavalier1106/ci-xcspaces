<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 字符串处理函数库，包含日期处理和数组等数量处理
 * 类似PHP自带函数的前面加_符号，其它用驼峰型样式命名
 */

/**
 * 支持所有字符或数组转换为JSON
 * @param mixed $arr 数组或字符串等可json化的东东
 * @param string $encoding 编码字符集,如gbk,utf-8
 * @return string
 */
function _json_encode($arr, $encoding='')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    return json_encode($encoding != 'utf-8' ? any2utf8($arr, $encoding) : $arr);
}

/**
 * 拆分一个字符串为数组，类似explode，支持中文分割符
 * @param $delimiter 分割符
 * @param $string 将要拆分的字符串
 * @param string $encoding 编码字符集,如gbk,utf-8
 * @return array
 */
function _explode($delimiter, $string, $encoding = '')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    mb_internal_encoding($encoding);
    $tmp = $string;
    
    $rt = array();
    
    do{
        $pos = mb_strpos($tmp, $delimiter);
        if($pos === false)
        {
            $rt[] = $tmp;
            break;
        }
        $sub = mb_substr($tmp, 0, $pos);
        $tmp = mb_substr($tmp, $pos+1);
        
        $rt[] = $sub;
        
    }while ($tmp);
    
    return $rt;
}


/**
 * 将其它字符编码转换为UTF8
 * @param array|string $var
 * @param string $encoding 编码字符集,如gbk,utf-8
 * @return array|string
 */
function any2utf8($var, $encoding = null)
{
    if($encoding=='utf-8')return $var;
    
    if(is_array($var))
    {
        foreach($var as &$val)
            $val = any2utf8($val, $encoding);
    
    }elseif(is_string($var))
    {
        //if($encoding == null)
        //    $encoding = getEncoding($var);
        if($encoding != "utf-8")
            $var = iconv($encoding, 'utf-8', $var);
            
        return $var;
    }
    
    return $var;
}

/**
 * 功能与JS的search相同
 * 返回正则表达式搜索中第一个子字符串匹配项的位置。
 * @param string $pattern 正则表达式
 * @param string $string
 * @return int
 */
function search($pattern, $string)
{
    preg_match($pattern, $string,  $reg);
    if(!$reg)return -1;
    return strpos($string, $reg[0]);
}

/**
 * 和JS的escape函数功能一样
 * @param string $str 要转义的内容
 * @param string $encoding 字符编码
 * @return string
 */
function escape($str, $encoding='')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    $r = array();
    preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (ord($v[0]) < 128) {
            $ar[$k] = rawurlencode($v);
        } else {
            $ar[$k] = "%u" . bin2hex(iconv($encoding, "UCS-2", $v));
        }
    }
    return implode("", $ar);
}

/**
 * 和JS的unescape函数功能一样
 * @param string $str
 * @param string $encoding 字符编码
 * @return string 解码结果
 */
function unescape($str, $encoding='')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    $r = array();
    $str = rawurldecode($str);
    preg_match_all("/(?:%u.{4})|.+/", $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == "%u" && strlen($v) == 6)
            $ar[$k] = iconv("UCS-2", $encoding, pack("H4", substr($v, -4)));
    }
    return join("", $ar);
}

/**
 * cookie编码，防止中文乱码
 * @param string $str cookie值
 * @return string
 * @use JS解码函数
 * function DecodeCookie(str){
 *     var strArr;
 *     var strRtn="";
 *     strArr=str.split("a");
 *     for (var i=strArr.length-1;i>=0;i--)
 *     strRtn+=String.fromCharCode(eval(strArr[i]));
 *     return strRtn;
 *   }
 */
function cookie_encode($str)
{
    $i = mb_strlen($str, 'EUC-CN') - 1;
    $arr = array();
    for (; $i >= 0; $i--)
        $arr[] = hexdec(
            bin2hex(
                mb_convert_encoding(mb_substr($str, $i, 1, 'UTF-8'), 'UCS-2', 'UTF-8')));
    return implode($arr, 'a');
}

/**
 * 获取一些随机字母
 * @param int $num 长度
 * @return string a-z
 */
function getRand($num = 5)
{
    $srcstr = "abcdefghijkmnpqrstuvwxyz";
    mt_srand();
    $strs = "";
    for ($i = 0; $i < $num; $i++)
        $strs .= $srcstr{mt_rand(0, 23)};

    return $strs;
}

/**
 * 是否为有效的URL
 * @param string $url 支持http|https|ftp协议
 * @return boolean
 */
function isValidUrl($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * 是否有效的邮箱地址
 * @param string $email
 * @return bool
 */
function isValidMail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * 是否有效的IP地址，支持IPv4, IPv6
 * @param string $ip
 * @return bool
 */
function isValidIP($ip)
{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * 按中文和英文长度同等比例截取字符串
 *  $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
 *  $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
 *  $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
 *  $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
 * @param string $str 要截取的字符串
 * @param int $len 截取长度
 * @param string $encoding 字符编码
 * @param string $char 有截取后加的修饰符号
 * @return string
 */
function cutString($str, $len, $encoding='', $char='…')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    if (strlen($str) < $len)
        return $str;
    mb_internal_encoding($encoding);
    $s='';
    $i2=0;
    $slen=mb_strlen($str);
    for($i=0; $i<$slen; $i++)
    {
      $one = mb_substr($str, $i, 1);

      if(strlen($one)==2){$i2++;}
      if($i2>=$len)break;
        $s.=$one;
        $i2++;
    }
    return $s.$char;
}

/**
 * 将中文转换成&#12343这样的编码格式
 * @param string $str 要转换的内容
 * @param string $encoding 要转换内容的编码
 * @return string
 */
function html_encode($str, $encoding='')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    return mb_convert_encoding($str, 'HTML-ENTITIES', $encoding);
}

/**
 * 安全过滤字符串，防止SQL注入
 * _htmlspecialchars与_addslashes函数的合集
 * @param string|array $str
 * @return string|array
 */
function g($str)
{
    return _addslashes(_htmlspecialchars(_trim($str)), true);
}

/**
 * 扩展htmlspecialchars($str,ENT_QUOTES)函数，支持数组转换
 * 注意,本函数会将'号转换为&#39;
 * @param mixed $str 数组或字符串
 * @return mixed 转换后的结果
 */
function _htmlspecialchars($str)
{
    if (is_array($str))
        return array_map('_htmlspecialchars', $str);
    
    return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * 扩展addslashes函数，支持多维数组
 * 当MAGIC_QUOTES_GPC已经开启时不会转义,需要设置$force参数
 * @param array|string $var 要转义的变量
 * @param bool $force 是否强制转义
 * @return array|string 处理后的$var
 */
function _addslashes($var, $force=false)
{
    if (MAGIC_QUOTES_GPC && !$force)
        return $var;

    if (is_array($var))
    {
        foreach($var as &$v)
        {
            $v = _addslashes($v, $force);
        }
        return $var;
    }

    return addslashes($var);

}

/**
 * 过滤$haystack中的$trimChar所包含的字符
 * @param string $haystack 要过滤的字符串
 * @param string|array $trimChar 要过滤的内容,默认为一些符号
 * @return string
 */
function str_filter($haystack , $trimChar = '')
{
    if(!$trimChar)
        $trimChar = array('"',"'",',','<','>',"\\",'/',';',':','[',']','{','}','=','+','`','~','!','#','$','%','^','*','(',')','?','|');
    
    $haystack = str_replace($trimChar, '', $haystack);
    return $haystack;
}

/**
 * 获取标准的字符内容,包含中文,英文字母,数字
 * @param string $word 原内容
 * @param string $affix 附加保留内容,[正则表达式]
 * @param string $encoding 当前内容编码
 * @return string
 */
function get_normal_word($word, $affix='',$encoding='')
{
    if(!$encoding)
    {
        global $CONFIG;
        $encoding = $CONFIG['html_charset'];
    }
    $conv = ($encoding != 'utf-8');
    $word = preg_replace('`[^\x{4E00}-\x{9FA5}\x{F900}-\x{FA2D}a-zA-Z0-9'.$affix.'\s]`u', '', 
        $conv ? iconv($encoding ,'utf-8', $word) : $word);
        
    return $conv ? iconv('utf-8', $encoding, $word) : $word;
}

/**
 * 将内容转换成简单并安全的格式，用于储存在数据库中或其它内容传输
 * @param array|string|int|float $res
 * @param bool $auto_stripslashes 是否去除转义
 * @return string
 */
function encode_data($res, $auto_stripslashes=true)
{
    if(is_array($res))
    {
        $delimiter = "\t";
        $header = array();
        $isMulti = false;
        foreach($res as $line)
        {
            if(is_array($line))
            {
                $isMulti = true;
                break;
            }
        }
        if($isMulti)
        {//多行的情况
            $lines = array();
            foreach($res as $line)
            {
                if(!$header)
                {
                    $header = array_keys($line);
                    echo implode($delimiter, $header)."\n";
                }
                //确保每一行都能被正确输出，格式必须是正确的
                $row = array();
                foreach($header as $key) //必须转义，否则有可以内容中有分界符，导致不可预料的事发生
                    $row[$key] = DB::encode($line[$key], $auto_stripslashes);

                $lines[] = implode($delimiter, $row);
                unset($row);
            }
            unset($header);
            return implode("\n", $lines);
        }elseif($res){ //单行
            $header = array();
            $row = array();
            foreach($res as $k=>$v)
            {
                $header[] = $k;
                $row[] = DB::encode($v, $auto_stripslashes);
            }
            $data = implode($delimiter, $header)."\n";
            $data .= implode($delimiter, $row);
            return $data;
        }
    }

    //其它
    return $res;
}

/**
 * 解码由encode_data编码后的数据
 * @param string $text
 * @param bool $autoSingleLine 是否自动转换成一维数组
 * @return array|string|int|float
 */
function decode_data($text, $autoSingleLine=true)
{
    $rows = explode("\n", $text);
    $len = count($rows);
    if($len < 2) //不是数组
        return $rows[0];
    $delimiter = "\t";
    $header = explode($delimiter, $rows[0]);
    $data = array();
    for($i=1; $i<$len; $i++)
    {
        $row = explode($delimiter, $rows[$i]);
        $arr = array();

        foreach($header as $index=>$key)
        {
            $arr[$key] = DB::decode($row[$index]);
        }
        $data[] = $arr;
    }
    if($autoSingleLine && $len == 2)
        return $data[0];
    return $data;
}

/**
 * 将一段字符串展开为数组
 * @param $string
 * @param string $lineChar 行分割字符
 * @param string $spChar key和value分割字符
 * @return array
 */
function strtoarray($string, $lineChar=",", $spChar=":")
{
    $rows = explode($lineChar, $string);
    $ret = array();
    foreach($rows as $row)
    {
        $row = trim($row);
        if(!$row)continue;
        $exp = explode($spChar, $row);
        if(count($exp) == 2)
            $ret[trim($exp[0])] = trim($exp[1]);
        else//如果为非K/V方式,如A:B:C
            $ret[] = $exp;
    }
    return $ret;
}