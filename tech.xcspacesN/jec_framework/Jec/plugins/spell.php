<?php
/**
 * 修改自网上代码，by jecelyin
 * 功能：转换中文为拼音
 */
class spell
{
    private static $data = array();
    
    /**
     * 转换为拼音函数
     * @param string $str 要转换的中文，可以为多个字
     * @param string $sp 每个文字分割符号
     * @return string
     */
    public static function conv($str, $sp = ' ')
    {
        $restr = $_sp = '';
        $str = trim($str);
        $slen = strlen($str);
        if($slen<2)return $str;
        if(!self::$data)
        {
            $data = file(APP_PATH . 'assets' . DS .'pinyin.dat');
            foreach ($data as $val)
            {
                $exp = explode('`', $val);
                self::$data[$exp[0]] = trim($exp[1]);
            }
        }
        
        for($i=0;$i<$slen;$i++)
        {
            if(ord($str[$i])>0x80)
            {
                $c = $str[$i].$str[$i+1];
                $i++;
                if(isset(self::$data[$c]))
                {
                        $restr .= $_sp . self::$data[$c];
                }else
                {
                    $restr .= $sp;
                }
                $_sp = $sp;
            }else
            {
                $restr .= $str[$i];
            }
        }
        
        return $restr;
    }

}


