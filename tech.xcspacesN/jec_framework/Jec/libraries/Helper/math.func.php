<?php

/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 * 数字和日期处理函数库
 */
/**
 * 返回一个整数
 * @param mixed $var 字符串或数字或数组
 * @return int
 */
function parseInt($var)
{
    if (is_array($var))
        return array_map('parseInt', $var);
    return (int)$var;
}

/**
 * 返回一个浮点数 注:.9 这样方式将解析为0
 * @param mixed $var 字符串或数字或数组
 * @return float
 */
function parseFloat($var)
{
    if (is_array($var))
        return array_map('parseFloat', $var);
    return (float)$var;
}

/**
 * 格式化数字为更加可观的数字表达方式，
 * @param float $num
 * @param int $style 样式：0使用title提示，-1直接显示无换行，-2直接显示有换行
 * @param string $tooltip 附加提示
 * @return string 返回格式如：1234.12, 12345.12(1万)
 */
function formatNumber($num, $style=0, $tooltip='')
{
    $moneyName = array(
        '亿' => pow(10,8),
        '万' => pow(10,4)
    );
    $suffix_name = '';
    $prefix_sign = $num < 0 ? '-' : '';
    $num = abs($num);

    foreach($moneyName as $name => $start)
    {
        if($num >= $start)
        {
            $snum=$num/$start;
            $p=substr($snum, strpos($snum,'.')+1, 1);
            $snum=floor($snum);
            if($p >= 5)
                $snum.='.'.$p;
            $suffix_name = " (".$prefix_sign.$snum.$name.")";
            break;
        }
    }

    if($style == -1 || $style == -2)
    {
        $br = $style == -2 ? "<br />" : "";
        return number_format($prefix_sign.$num).($suffix_name ? $br."<em>{$suffix_name}</em>" : '');
    }elseif($style == -3){
        return $suffix_name;
    }
    return '<span title="'.$tooltip.($suffix_name?$prefix_sign.$num.$suffix_name:$prefix_sign.$num).'">'.number_format($prefix_sign.$num).'</span>';
}

/**
 * 除法运算，当$r==0时，返回0,不报错
 * @param int|float $l
 * @param int|float $r
 * @param int $precision 四舍五入倍数
 * @param int $percent 百分比
 * @return float|int
 */
function div($l, $r, $precision=0, $percent=1)
{
    if($r == 0)return 0;
    return round($l/$r, $precision)*$percent;
}

/*++++日期函数+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

/**
 * 返回已添加指定时间间隔的日期。
 * 可用 DateAdd 函数从日期中添加或减去指定时间间隔。
 * 例如可以使用 DateAdd 从当天算起 30 天以后的日期或从现在算起 45 分钟以后的时间。
 * 要向 date 添加以“日”为单位的时间间隔，可以使用“一年的日数”（“y”）、“日”（“d”）或“一周的日数”（“w”）。
 * DateAdd 函数不会返回无效日期。如下示例将 95 年 1 月 31 日加上一个月：
 *    $newDate = DateAdd("m", 1, "31-Jan-95")
 * 在这个例子中，DateAdd 返回 95 年 2 月 28 日，而不是 95 年 2 月 31 日。如果 date 为 96 年 1 月 31 日，则返回 96 年 2 月 29 日，这是因为 1996 是闰年。
 * 如果计算的日期是在公元 100 年之前则会产生错误。
 * 如果 number 不是 Long 型值，则在计算前四舍五入为最接近的整数。
 * @param string $interval 表示要添加的时间间隔,有以下值
 *      yyyy    年
 *       q      季度
 *       m      月
 *       y      一年的日数
 *       d      日
 *       w      一周的日数
 *       ww     周
 *       h     小时
 *       n     分钟
 *       s     秒
 * w、y和d的作用是完全一样的，即在目前的日期上加一天，q加3个月，ww加7天。
 * @param int $number 表示要添加的时间间隔的个数。数值表达式可以是正数（得到未来的日期）或负数（得到过去的日期）。
 * @param string|int $date 任何标准的日期表示式
 * @throws JecException
 * @return int
 */
function dateAdd($interval, $number, $date)
{
    if(!is_numeric($date))
        $date=strtotime($date);
    $date_time_array = getdate($date);
    $hours = $date_time_array["hours"];
    $minutes = $date_time_array["minutes"];
    $seconds = $date_time_array["seconds"];
    $month = $date_time_array["mon"];
    $day = $date_time_array["mday"];
    $year = $date_time_array["year"];
    switch ($interval)
    {
        case "yyyy":
            $year += $number;
            break;
        case "q":
            $month += ($number * 3);
            break;
        case "m":
            $month += $number;
            break;
        case "y":
        case "d":
        case "w":
            $day += $number;
            break;
        case "ww":
            $day += ($number * 7);
            break;
        case "h":
            $hours += $number;
            break;
        case "n":
            $minutes += $number;
            break;
        case "s":
            $seconds += $number;
            break;
        default:
            throw new JecException("the dateDiff function has a bad parameter/0: $interval");
    }
    $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
    return $timestamp;
}


/**
 * 是否是今天的时间
 * @param string|int $time 时间
 * @return bool
 */
function isToday($time)
{
    if (!is_numeric($time))
        $time = strtotime($time);
    return date('Ymd') == date('Ymd', $time);
}

/**
 * 转换秒数为中文描述
 * @param  $diff 秒数
 * @return string
 */
function secondToString($diff)
{
    if ($diff < 60) {
        return intval($diff % 60) . "秒";
    } elseif ($diff < 60 * 15)
    {
        return intval($diff / 60) . "分钟";
    } elseif ($diff < 60 * 30)
    {
        return "一刻钟";
    } elseif ($diff < 60 * 60)
    {
        return "半小时";
    } elseif ($diff < 60 * 60 * 24)
    {
        return intval($diff / 60 / 60) . "小时";
    } elseif ($diff < 60 * 60 * 24 * 7)
    {
        return intval($diff / 60 / 60 / 24) . "天";
    } elseif ($diff < 60 * 60 * 24 * 30)
    {
        return intval($diff / 60 / 60 / 24 / 7) . "星期";
    } elseif ($diff < 60 * 60 * 24 * 365)
    {
        return intval($diff / 60 / 60 / 24 / 30) . "个月(".intval($diff / 60 / 60/ 24)."天)";
    } else
    {
        return intval($diff / 60 / 60 / 24 / 365) . "年(".intval($diff / 60 / 60/ 24)."天)";
    }
}

/**
 * 格式化一个时间，date函数的扩展使用
 * @param int|string $timestamp null则使用当前时间，否则必须为一个unix时间戵或一个可以strtotime函数转换的时间描述
 * @param string $format 和date函数的format参数一致
 * @return string
 */
function getDateStr($timestamp=null, $format='Y-m-d H:i:s')
{
    if($timestamp === null)
        $timestamp = TIME;
    if(!$timestamp)
        return '';
    if(!is_numeric($timestamp)){
        $timestamp = strtotime($timestamp);
    }
    return date($format, $timestamp);
}

/**
 * 获取某天0点整的时间描述
 * @param $ts 可以为一个unix时间戵或一个可以strtotime函数转换的时间描述
 * @return string Y-m-d 00:00:00
 */
function getStartTimeOfDay($ts)
{
    if(is_numeric($ts))
        $ts = date('Y-m-d',$ts);
    else
        $ts = substr($ts, 0, 10);

    return $ts.' 00:00:00';
}

/**
 * 获取某天23:59:59的时间描述
 * @param $ts 可以为一个unix时间戵或一个可以strtotime函数转换的时间描述
 * @return string
 */
function getEndTimeOfDay($ts)
{
    if(is_numeric($ts))
        $ts = date('Y-m-d',$ts);
    else
        $ts = substr($ts, 0, 10);

    return $ts.' 23:59:59';
}

/**
 * 获取传入时间是一个星期中的星期几
 * @param int|string $ts 可以为一个unix时间戵或一个可以strtotime函数转换的时间描述
 * @param string $style 风格，只能是cn,en,en_full
 * @return string 星期几
 */
function getWeek($ts, $style='cn')
{
    if(!is_numeric($ts))
        $ts = strtotime($ts);

    if($style === 'cn')
    {
        //w: 0（表示星期天）到 6（表示星期六）
        $weekName = array('日','一','二','三','四','五','六');
        return $weekName[date('w', $ts)];
    }elseif($style == 'en'){ //星期中的第几天，文本表示，3 个字母	Mon 到 Sun
        return date('D', $ts);
    }elseif($style == 'en_full'){ //星期几，完整的文本格式	Sunday 到 Saturday
        return date('l', $ts);
    }
    return '';
}

/**
 * 返回一年中每几周的周一到周日的时间范围
 * @param int $year 年份
 * @param int $week_num 一年中的每几周
 * @return array (Y-m-d, Y-m-d)
 */
function getWeekRange($year, $week_num)
{
    $week_sec = 7*24*3600;
    $year1 = strtotime("{$year}-01-01");
    $first = $year1 - date('w', $year1)*24*2600;
    //周数的星期天的日期
    $et = $first+$week_num*$week_sec;
    //注意这里的$et是星期天的0点时间，-7天时间就是上个星期天的，我们要加一天从星期一开始
    $st = $et - $week_sec + 24*3600;
    if($st<$year1)
        $st=$year1;
    return array(date('Y-m-d',$st), date('Y-m-d',$et));
}

/**
 * 获取一周的开始星期一的日期
 * @param int|string $ts
 * @return string Y-m-d
 */
function getMonday($ts)
{
    if(!$ts)
        return '0000-00-00';
    if(!is_numeric($ts))
        $ts = strtotime($ts);
    //w	星期中的第几天，数字表示	0（表示星期天）到 6（表示星期六）
    $dayofweek = date('w', $ts);
    if($dayofweek != 1)
        $ts = strtotime('-1 monday', $ts); //注意：当$ts不是星期一时，它会移到上个星期一
    return date('Y-m-d', $ts);
}

/**
 * 正确地获取相对于$ts时间的$rel个月的日期
 * 注意：不能直接使用strtotime('-1 month')，因为2012-05-31将得到的时间是2012-05-01
 * @param int $rel 偏移的月数
 * @param null $ts 相对时间点
 * @return int 那个月的日期，如ts=2012.05.31,$rel=-1，那么返回2012.04.30
 */
function getMonth($rel=1, $ts=null)
{
    if(!$ts)
        $ts = TIME;
    elseif(!is_numeric($ts))
        $ts = strtotime($ts);

    $firstday = strtotime(date('Y-m-01', $ts));
    $month = strtotime("$rel month", $firstday);
    //获取偏移月的最后一天
    $lastday = strtotime('+1 month', $month) - 24*3600;
    //比较日期
    $lastday_d = date('d', $lastday);
    $ts_d = date('d', $ts);
    if($ts_d > $lastday_d)
        $ts_d = $lastday_d;

    return strtotime(date('Y-m-', $month).$ts_d.date(' H:i:s', $ts));
}

/**
 * 根据时间段返回日期数组
 * @param int|string $st 开始时间
 * @param int|string $et 结束时间
 * @return array 日期数组
 */
function getDateRange($st=TIME, $et=TIME)
{
    $date = array();
    if(!is_numeric($st))
        $st = strtotime($st);
    if(!is_numeric($et))
        $et = strtotime($et);
    if($st > $et)
        list($st,$et) = array($et,$st);

    while($st <= $et)
    {
        $date[] = getDateStr($st,'Y-m-d');
        $st = strtotime('1 day',$st);
    }
    return $date;
}