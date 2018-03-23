<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 验证码类
 */
class Captcha
{
    public $width = 250; //图像宽度
    public $height = 50; //图像高度
    public $font = 'ariblk.ttf'; //字体
    public $num = 8; //8, 字符个数
    public $fontSpace = 30; //字体间距
    public $fontY = 40; //字体Y轴
    public $fontSize = 26; //字体大小
    public  $sessKey = 'validCode'; //验证码sesseion key

    /**
     * @param int $num 验证码个数
     */
    public function __construct($num=4)
    {
        $this -> font = APP_PATH . DS . 'assets' . DS . $this -> font;
        $this->num = $num;
        
        if($num == 4)
        {
            $this->width = 120;
            $this->height = 30;
            $this->fontY = 25;
            $this->fontSize = 20;
        }
    }

    /**
     * 获取随机生成的验证码
     * @return string
     */
    public function getValue()
    {
        return $_SESSION[$this->sessKey];
    }

    /**
     * 直接输出到浏览器并显示为图片
     */
    public function showImage()
    {
        if(!session_id())session_start();
        Net::sendNoCache();
        header("Content-type: image/png");
        $txt = $this->makeValidCode();
        $this->makeImage($txt, 'png');
    }

    /**
     * 创建随机验证码
     * @return string
     */
    public function makeValidCode()
    {
        $text = $this -> _generateCode();
        $_SESSION[$this->sessKey] = $text;
        return $text;
    }


    /**
     * 创建验证码图片，若指定文件名则生成文件，否则直接输出图片数据
     * @param string $text 验证码内容
     * @param string $type 图片类型
     * @param string | null $filename 生成文件名称，默认为 null
     * @param integer $quality 图片质量1-100，默认为100
     */
    public function makeImage($text, $type='png', $filename=NULL, $quality=100)
    {
        if(!in_array($type, array('png', 'jpg', 'gif','jpeg')))
            exit('bad type');
        if($type=='jpg')$type='jpeg';
        
        $im = imagecreatetruecolor ($this -> width, $this -> height);
        $background_color = imagecolorallocate($im, 255, 255, 255);
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $this -> width - 1, $this -> height - 1, $background_color);
        imagerectangle($im, 0, 0, $this -> width-1, $this -> height-1, $borderColor);
        // 添加干扰
        for($i=0;$i<30;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,233),mt_rand(0,233),mt_rand(0,233));
            imagearc($im,mt_rand(-10,$this -> width),mt_rand(-10,$this -> height), rand(30,150), rand(30,150), 90, 270,$fontcolor);
        }
        
        $text_len = strlen($text);
        for($i=0; $i<$text_len; $i++)
        {
            //验证码颜色,注意这里随机值太大的话,会产生偏白的验证码,不利于用户体验
            $text_color=imagecolorallocate($im,mt_rand(0,155),mt_rand(0,155),mt_rand(0,155));
            //$x = 10 + $i * 30;
            $x = 10 + $i * $this -> fontSpace;
            $angle = rand(1,1000) > 600 ? rand(350, 360) : rand(0, 15);
            //imagettftext($im, 26, $angle, $x, 40, $text_color, $this -> font, $text{$i});
            imagettftext($im, $this -> fontSize, $angle, $x, $this -> fontY, $text_color, $this -> font, $text{$i});
        }
        
        //imagestringup ($im, 5, 5, 5,  $text, $text_color);
        $func='image'.$type;
        $func($im, $filename, $quality);
        
        imagedestroy($im);
    }
    
    private function _generateCode()
    {
        $possible = '2345689abcdefghjkmnpqrstwxyz';
        $code = '';
        $plen = strlen($possible)-1;
        for($i = 0; $i < $this -> num; $i++)
            $code .= $possible{rand(0, $plen)} ;
            
        return $code;
    }

}
