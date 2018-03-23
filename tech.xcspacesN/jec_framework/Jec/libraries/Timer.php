<?php
/**
 * User: jecelyin
 * Date: 12-3-9
 * Time: 下午4:18
 * 计时类，支持嵌套
 */
class Timer
{
    private static $st = array();
    private static $et = array();

    public static function start()
    {
        self::$st[] = microtime(true);
    }

    public static function end($tag, $return_type=false)
    {
        self::$et[] = microtime(true);

        $st = array_pop(self::$st);
        $et = array_pop(self::$et);

        $sec = (float)bcsub($et, $st, 5); //不要直接减，PHP计算浮点数会显示成科学计算法
        //$sec = sprintf("%.5f", $et - $st);
        if($return_type)
        {
            return "##{$tag}: {$sec} sec at ".date('Y-m-d H:i:s',$st).'~'.date('Y-m-d H:i:s',$et);
        } else {
            echo "##{$tag}: {$sec} sec at ".date('Y-m-d H:i:s',$st).'~'.date('Y-m-d H:i:s',$et);
            if(isCLI())
                echo "\n";
            else
                echo "<br />";
        }
    }
}
