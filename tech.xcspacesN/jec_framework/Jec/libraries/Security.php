<?php
/**
 * User: jecelyin QQ:249828165
 * Date: 12-2-3
 * Time: 下午5:24
 *
 */
 
class Security
{
    private static function _xor($str1, $pw)
    {
        $c = 0;
        $liste = array();
        $len = strlen($str1);
        $plen = strlen($pw);
        for($i=0;$i<$len;$i++)
        {
            if($c > $plen-1)
                $c = 0;

            $fi = ord($pw{$c});
            $c++;
            $se = ord($str1{$i});
            $fin = $fi ^ $se;
            $liste[] = chr($fin);
        }
        return $liste;
    }

    /**
     * 加密一段字符串，密码后的字符串长度受加密的内容长度影响，即为返回不定长的结果
     * @static
     * @param $data 将被加密的数据，建议为非特殊字符
     * @param $secret_string 加密密钥
     * @return bool|string 成功则返回字符串，否则返回false
     */
    public static function encrypt($data, $secret_string)
    {
        //mcrypt_encrypt的KEY不能超过24
        if(strlen($secret_string) > 24)
            $secret_string = substr($secret_string,0,24);
        try{
            $data = base64_encode($data);
            if(function_exists('mcrypt_encrypt'))
            {
                srand((double) microtime() * 1000000);
                $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
                return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret_string, $data, MCRYPT_MODE_ECB, $iv));
            }else{
                $ret = self::_xor($data, $secret_string);
                return base64_encode(implode('',$ret));
            }
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 解密一段字符串
     * @static
     * @param $data 将要解密的数据
     * @param $secret_string 加密密钥
     * @return bool|string 成功则返回字符串，否则返回false
     */
    public static function decrypt($data, $secret_string)
    {
        //mcrypt_encrypt的KEY不能超过24
        if(strlen($secret_string) > 24)
            $secret_string = substr($secret_string,0,24);
        try{
            $data = base64_decode($data);
            if(function_exists('mcrypt_encrypt'))
            {
                $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
                return base64_decode(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret_string, $data, MCRYPT_MODE_ECB, $iv)));
            }else{
                $ret = self::_xor($data, $secret_string);
                return base64_decode(implode('', $ret));
            }

        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 对明文密码进行加密
     * @param $pwd 明文密码
     * @return string 加密后的密码
     */
    public static function password($pwd)
    {
        return md5(sha1($pwd.'Jec+*+#@').'JecPassword*+!#!~$@#');
    }

}