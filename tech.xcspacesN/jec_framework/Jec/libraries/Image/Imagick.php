<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * Imagick图像处理驱动
 */
class Image_Imagick
{
    public $opts = array();
    private $srcIm = null;
    private $srcWidth = 0;
    private $srcHeight = 0;
    private $srcFile = '';
    private $srcExt = '';
    
    public function __construct($opts)
    {
        $this->opts = $opts;
    }
    
    public function init($srcFile)
    {
        $this->srcFile = $srcFile;

        try {
            $this->srcIm   = new Imagick($srcFile);
            $page = $this->srcIm->getImagePage();
            $this->srcWidth  = (int)$page['width'];
            $this->srcHeight = (int)$page['height'];

            $this->srcExt    = strtolower($this->srcIm->getImageFormat());
            $this->srcIm->setFormat($this->srcExt);
            //合并gif图片，如果是jpg等其它图片则也合并下所有图层
            $this->srcIm = $this->srcIm->coalesceImages();
        }
        catch (Exception $e) {
            return false;
        }
        if (!$this->srcExt)
            return false;
        
        return true;
    }
    
    /**
     * 获取宽度，高度，扩展名
     * @param string $srcFile
     * @return bool|array()
     */
    public function getInfo($srcFile)
    {
        if(!$this->init($srcFile))
            return false;
            
        return array('width'=>$this->srcWidth, 'height'=>$this->srcHeight, 'ext'=>$this->srcExt);
    }
    
    /**
     * 水印函数
     * @param string $srcFile 要进行水印的图片
     * @return bool
     */
    public function watermark($srcFile)
    {
        if (!$this->init($srcFile))
            return false;
        
        if (!$this->opts['wm_text'])
            return $this->_imageWatermark();
        
        return $this->_textWatermark();
    }
    
    /**
     * 图片水印
     * @return bool
     */
    private function _imageWatermark()
    {
        try{
            $img_wm = new Imagick($this->opts['wm_img']);
        }catch (Exception $e){
            return false;
        }

        $oriHeight = $img_wm->getImageHeight();
        $oriWidth  = $img_wm->getImageWidth();
        
        $pos = Image::getWatermarkPos(array(
            'srcW' => $this->srcWidth,
            'srcH' => $this->srcHeight,
            'destW' => $oriWidth,
            'destH' => $oriHeight,
            'pos' => $this->opts['wm_fontAlign']
        ));
        
        $x = $pos['x'];
        $y = $pos['y'];

        if ($this->srcHeight < $oriHeight || $this->srcWidth < $oriWidth)
            return false;
            
        //$dest = new Imagick();
        //$dest -> setFormat($this->srcExt);
        //$color_transparent = new ImagickPixel("transparent");
        foreach ($this->srcIm as $img)
            $img->compositeImage($img_wm, Imagick::COMPOSITE_OVER, $x, $y); //水印

        $img_wm->destroy();
        $this->srcIm->writeImages($this->srcFile, true);
        $this->srcIm->clear();
        $this->srcIm->destroy();
        
        return true;
    }
    
    /**
     * 文字水印
     * @return bool
     */
    private function _textWatermark()
    {
        $ndw       = new ImagickDraw();
        $fontColor = new ImagickPixel($this->opts['wm_fontColor']);
        
        $textEn = iconv("gbk", "utf-8", $this->opts['wm_text']); //如果你传入的是非UTF8中文，这里要转换
        $ndw -> setTextEncoding("UTF-8"); //设定图像上文字的编码
        $ndw -> setFont($this->opts['wm_font']); //设置字体文件，默认宋体
        $ndw -> setFontWeight($this->opts['wm_fontWidth']); //设定字宽
        $ndw -> setFillColor($fontColor); //设定字体颜色
        $ndw -> setFontSize($this->opts['wm_fontSize']); //设定字体大小
        $ndw -> setGravity($this->opts['wm_fontAlign']); //设定对齐方式 MW_ForgetGravity,MW_NorthWestGravity,MW_NorthGravity,MW_NorthEastGravity,MW_WestGravity,MW_CenterGravity,MW_EastGravity,MW_SouthWestGravity,MW_SouthGravity,MW_SouthEastGravity,MW_StaticGravity
        //$ndw -> setFillAlpha($this->opts['wm_fontAlpha']); //设置文字透明度 ,完全透明1.0
        
        $pos = Image::getWatermarkPos(array(
            'srcW' => $this->srcWidth,
            'srcH' => $this->srcHeight,
            'destW' => $this->opts['wm_fontSize'] * strlen($textEn) / 2, //评估宽度
            'destH' => $this->opts['wm_fontSize'],
            'pos' => $this->opts['wm_fontAlign']
        ));
        
        $x = $pos['x'];
        $y = $pos['y'];

        foreach ($this->srcIm as $img)
            $img->annotateImage($ndw, $x, $y, 0, $textEn); //写上文字水印

        $this->srcIm->clear();
        $this->srcIm->writeImages($this->srcFile, true);
        $this->srcIm->destroy();
        $fontColor -> clear();
        $ndw -> clear();
        $fontColor -> destroy();
        $ndw -> destroy();
        return true;
    }

    /**
     * 缩略图函数
     * @param array $opts
     * @return string 生成缩略图后的文件名
     */
    public function resize($opts)
    {
        $this -> init($opts['srcFile']);
        $maxWidth             = $opts['maxWidth'];
        $maxHeight            = $opts['maxHeight'];
        
        if($this->srcWidth < $maxWidth && $this->srcHeight < $maxHeight)
        {
            @copy($opts['srcFile'], $opts['destFile']);
            return $opts['destFile'];
        }

        $wh = Image::getResizeWH($maxWidth, $maxHeight, $this->srcWidth, $this->srcHeight);
        
        foreach ($this->srcIm as $img) {
            /*$page = $img->getImagePage();
            
            $w = $page['width'];
            $h = $page['height'];
            echo "$w - $h <br>";
            if ($w < $maxWidth && $h < $maxHeight)
                continue;
            
            $wh = Image::getResizeWH($maxWidth, $maxHeight, $w, $h);
            */
            if($this -> opts['roundCorners'] 
            && $img->getImageWidth() == $this->srcWidth 
            && $img->getImageHeight() == $this->srcHeight)
                $this -> roundedCorner($img);//$img->roundCorners($this->opts['roundCorners_x'], $this->opts['roundCorners_y']);

            $img->thumbnailImage($wh['width'], $wh['height'], true);

        }

        $this->srcIm->writeImages($opts['destFile'], true);
        
        $this->srcIm->clear();
        $this->srcIm->destroy();
        
        return $opts['destFile'];
    }
    
    /**
     * 圆角处理
     * @param resource $im
     */
    private function roundedCorner(&$im)
    {
        $im2 = new imagick(APP_PATH . '/assets/rounded_corner.png');

        $rotate = array(0,90,180,270);
        $pixel = new ImagickPixel('transparent'); //#ffffff
        //循环合并到图片四个角
        foreach($rotate as $r)
        {
            if($r)
                $im -> rotateImage($pixel, $r);//MagickRotateImage($im, $pixel ,$r);
            $im -> compositeImage($im2, Imagick::COMPOSITE_OVER, 0, 0);
        }
        
        $im -> rotateImage($pixel, 180);
        //return $im;
    }
    
}//end class