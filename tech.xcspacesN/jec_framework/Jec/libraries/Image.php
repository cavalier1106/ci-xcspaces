<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 图像处理类
 */

class Image
{
    private $_opts = array(
        'driver' => 'Imagick', //MW, GD, Imagick
        'wm_img' => 'watermark.png', //水印图片地址
        'wm_text' => '', //文字水印，优先使用图片水印
        'wm_fontSize' => "30", //字体大小
        'wm_fontColor' => "#FFFFFF", //字体颜色
        'wm_fontWidth' => 100, //字宽
        'wm_fontAlpha' => 0.3, //字符透明度
        'wm_fontAlign' => 5, //水印位置 ,0为随机位置；
        //                         1为顶端居左，2为顶端居中，3为顶端居右；
        //                         4为中部居左，5为中部居中，6为中部居右；
        //                         7为底端居左，8为底端居中，9为底端居右；
        'wm_font' => 'arial.ttf', //文字水印字体
        
        'thumb_suffix' => '_thumb', //缩略图后缀
        'thumb_prefix' => '', //缩略图前缀
        'roundCorners' => 0, //是否开启缩略图后圆角处理
        'roundCorners_x' => 20, //圆角x轴角度
        'roundCorners_y' => 20, //圆角y轴角度
    );
    
    //缓存默认选项
    private $_srcOpts = array();
    /**
     * @var Image_Imagick 或其它驱动
     */
    private $_drv = null;
    
    /**
     * @param array $opts 相关参数
     */
    public function __construct($opts = array())
    {
        global $CONFIG;
        
        $this->_opts['wm_font'] = APP_PATH . DS . 'assets' . DS . $this->_opts['wm_font'];
        $this->_opts['wm_img']  = APP_PATH . DS .'assets' . DS . $this->_opts['wm_img'];
        
        if($opts)
            $this -> setOptions($opts);
        else
            $this -> setOptions($CONFIG['image']);
            
        $this -> _srcOpts = $this->_opts;
        $drvName = "Image_{$this -> _opts['driver']}";
        
        $this -> _drv = new $drvName($this -> _opts);
    }

    /**
     * 设置选项
     * @param array $opts
     * @return null
     */
    public function setOptions($opts)
    {
        if(!$opts)
            return ;
        
        foreach($opts as $key => $val)
            $this -> _opts[$key] = $val;
            
        if($this -> _drv)
            $this -> _drv -> opts = $this -> _opts;
    }
    
    /**
     * 获取宽度，高度，扩展名
     * @param string $srcFile
     * @return bool|array()
     */
    public function getInfo($srcFile)
    {
        if(!is_file($srcFile))
            return false;
            
        return $this->_drv -> getInfo($srcFile);
    }
    
    /**
     * 调整图片大小
     * @param array $opts array(
     *     'srcFile'源图片,大图
     *     ,'maxWidth'最大宽度
     *     ,'maxHeight'最大高度
     *     ,'destFile'指定缩略图文件名,不指定则为原文件名+后缀
     * )
     * @return bool|string
     */
    public function resize($opts = array('srcFile'=>'', 'maxWidth'=>0,'maxHeight'=>0,'destFile'=>0))
    {
        if(!$opts['srcFile'] || !is_file($opts['srcFile']) || !self::isValidImage($opts['srcFile']))
            return false;
            
        //不指定目标文件
        if(!$opts['destFile'])
        {
            $pinfo = pathinfo($opts['srcFile']);
            if($this -> _opts['thumb_suffix'])
                $opts['destFile'] = $pinfo['dirname'] . DS . $pinfo['filename'] . $this -> _opts['thumb_suffix'] . '.' . $pinfo['extension'];
            else
                $opts['destFile'] = $pinfo['dirname'] . DS . $this -> _opts['thumb_prefix'] . $pinfo['basename'];
        }
        
        $thumb = $this -> _drv -> resize($opts);
        
        if($thumb)
        {
            $pinfo = pathinfo($thumb);
            return $pinfo['basename'];
        }
        
        return false;
    }
    
    /**
     * 水印函数,没有指定水印图片,则使用文字水印
     * @param string $srcFile 源文件地址
     * @param array $opts 使用iImage::$_opts数组
     * @return bool
     */
    public function watermark($srcFile, $opts = array())
    {
        if(!$srcFile || !is_file($srcFile) || !self::isValidImage($srcFile))
            return false;

        if(isset($opts['wm_font']))
            $opts['wm_font'] = APP_PATH . DS .'assets' . DS . $opts['wm_font'];
        if(isset($opts['wm_img']))
            $opts['wm_img']  = APP_PATH . DS .'assets' . DS . $opts['wm_img'];
        
        if($opts)
            $this -> setOptions($opts);
            
        return $this -> _drv -> watermark($srcFile);
    }
    
    /**
     * 获得缩进比例后的宽度和高度与缩放比例
     * @param int $maxW 缩略图宽度
     * @param int $maxH 缩略图高度
     * @param int $nowW 要处理的图片宽度
     * @param int $nowH 要处理的图片高度
     * @return array (width,height,scale)
     */
    public static function getResizeWH($maxW, $maxH, $nowW, $nowH)
    {
        if($nowW<$maxW && $nowH < $maxH)
            return array('width'=>$nowW, 'height'=>$nowH, 'scale'=>0);
        
        if ($maxW && $maxH) {
            $scale  = min($maxW / $nowW, $maxH / $nowH); // 计算缩放比例
            // 缩略图尺寸
            $maxW  = floor($nowW * $scale);
            $maxH = floor($nowH * $scale);
        } else {
            if ($maxW && !$maxH) { //按照宽度来缩图 
                $scale  = $maxW / $nowW;
                $maxH = floor($nowH * $scale);
            } else { //按照高度来缩图
                $scale = $maxH / $nowH;
                $maxW = floor($nowW * $scale);
            }
        }
        
        return array('width'=>$maxW, 'height'=>$maxH, 'scale'=>$scale);
    }
    
    /**
     * 根据文件名判断是否有效的图片
     * @param string $file
     * @return bool|string 成功则返回真实图片类型,即文件扩展名
     */
    public static function isValidImage($file)
    {
        if(!is_file($file))return false;
        $ext = '';
        if(extension_loaded('imagick'))
        {
            try{
                $im = new Imagick($file);
                $ext = $im -> getImageFormat();
                $im -> destroy();
            }catch(Exception $e){
                return false;
            }
        }elseif(extension_loaded('magickwand')){
            try{
                $nmw = NewMagickWand();
                MagickReadImage($nmw, $file);
                $ext = MagickGetImageFormat($nmw);
                DestroyMagickWand($nmw);
            }catch(Exception $e){
                return false;
            }
        }elseif(extension_loaded('gd')){//GD
             $types = array(
                1 => 'GIF',
                2 => 'JPG',
                3 => 'PNG',
                4 => 'SWF',
                5 => 'PSD',
                6 => 'BMP',
                7 => 'TIFF(intel byte order)',
                8 => 'TIFF(motorola byte order)',
                9 => 'JPC',
                10 => 'JP2',
                11 => 'JPX',
                12 => 'JB2',
                13 => 'SWC',
                14 => 'IFF',
                15 => 'WBMP',
                16 => 'XBM'
            );
            try{
                $data = getImageSize($file);
                $ext = $types[$data[2]];
                unset($data);
            }catch(Exception $e){
                return false;
            }
        }else{
            $pinfo = pathinfo($file);
            $ext = $pinfo['extension'];
        }
        
        $ext = strtolower($ext);
        
        return in_array($ext, array('gif','jpg','jpeg','png','bmp','psd','tiff')) ? $ext : false;
    }
    
    /**
     * 分析得到水印图片在大图中的位置
     * @param string $opts array(pos位置, srcW大图宽, srcH, destW水印图片宽, destH)
     * @return array('x','y')
     */
    public static function getWatermarkPos($opts)
    {
        $srcFile_w = $opts['srcW'];
        $srcFile_h = $opts['srcH'];
        $width     = $opts['destW'];
        $height    = $opts['destH'];
        
        switch ($opts['pos']) {
            case 0: //随机位置
                $wX = rand(0, ($srcFile_w - $width));
                $wY = rand(0, ($srcFile_h - $height));
                break;
            case 1: //左上角
                $wX = 5;
                $wY = 5;
                break;
            case 2: //左中
                $wX = 5;
                $wY = ($srcFile_h - $height) / 2;
                break;
            case 3: //左下
                $wX = 5;
                $wY = $srcFile_h - $height - 5;
                break;
            case 4: //上中
                $wX = ($srcFile_w - $width) / 2;
                $wY = 5;
                break;
            case 5: //正中
                $wX = ($srcFile_w - $width) / 2;
                $wY = ($srcFile_h - $height) / 2;
                break;
            case 6: //下中
                $wX = ($srcFile_w - $width) / 2;
                $wY = $srcFile_h - $height - 5;
                break;
            case 7: //右上
                $wX = $srcFile_w - $width - 5;
                $wY = 5;
                break;
            case 8: //右中
                $wX = $srcFile_w - $width - 5;
                $wY = ($srcFile_h - $height) / 2;
                break;
            case 9: //右下
                $wX = $srcFile_w - $width - 5;
                $wY = $srcFile_h - $height - 5;
                break;
            default: //中
                $wX = ($srcFile_w - $width) / 2;
                $wY = ($srcFile_h - $height) / 2;
        }
        return array('x' => $wX, 'y' => $wY);
    }
    
} // end class