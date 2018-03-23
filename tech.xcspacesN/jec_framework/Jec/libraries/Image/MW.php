<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * MagickWand for PHP 图像处理驱动
 */
class Image_MW
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
        $this->srcIm   = NewMagickWand();

        try {
            MagickReadImage($this->srcIm, $srcFile);
            $page = MagickGetImagePage($this->srcIm);
            $this->srcWidth  = (int)$page[0];
            $this->srcHeight = (int)$page[1];

            $this->srcExt    = strtolower(MagickGetImageFormat($this->srcIm));
            MagickSetFormat($this->srcIm, $this->srcExt);
            $this->srcIm = MagickCoalesceImages($this->srcIm);
        }
        catch (Exception $e) {
            return false;
        }
        if (!$this->srcExt)
            return false;
        
        return true;
    }
    
    /**
     * 水印函数
     * @param string $srcFile
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
        $img_wm = NewMagickWand();
        
        $wmFile = $this->opts['wm_img'];
        
        if (!MagickReadImage($img_wm, $wmFile))
            return false;
        
        $oriHeight = MagickGetImageHeight($img_wm);
        $oriWidth  = MagickGetImageWidth($img_wm);
        
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

        MagickResetIterator($this->srcIm);
        do {
            MagickCompositeImage($this->srcIm, $img_wm, MW_OverCompositeOp, $x, $y); //水印
        } while (MagickNextImage($this->srcIm));

        MagickWriteImages($this->srcIm, $this->srcFile, true);

        DestroyMagickWand($this->srcIm);
        DestroyMagickWand($img_wm);
        
        return true;
    }
    
    /**
     * 文字水印
     * @return bool
     */
    private function _textWatermark()
    {
        $ndw       = NewDrawingWand();
        $fontColor = NewPixelWand($this->opts['wm_fontColor']);
        
        $textEn = iconv("gbk", "utf-8", $this->opts['wm_text']); //如果你传入的是非UTF8中文，这里要转换
        DrawSetTextEncoding($ndw, "UTF-8"); //设定图像上文字的编码
        DrawSetFont($ndw, $this->opts['wm_font']); //设置字体文件，默认宋体
        DrawSetFontWeight($ndw, $this->opts['wm_fontWidth']); //设定字宽
        DrawSetFillColor($ndw, $fontColor); //设定字体颜色
        DrawSetFontSize($ndw, $this->opts['wm_fontSize']); //设定字体大小
        DrawSetGravity($ndw, $this->opts['wm_fontAlign']); //设定对齐方式 MW_ForgetGravity,MW_NorthWestGravity,MW_NorthGravity,MW_NorthEastGravity,MW_WestGravity,MW_CenterGravity,MW_EastGravity,MW_SouthWestGravity,MW_SouthGravity,MW_SouthEastGravity,MW_StaticGravity
        //DrawSetFillAlpha($ndw, $this->opts['wm_fontAlpha']); //设置文字透明度 ,完全透明1.0
        
        $pos = Image::getWatermarkPos(array(
            'srcW' => $this->srcWidth,
            'srcH' => $this->srcHeight,
            'destW' => $this->opts['wm_fontSize'] * strlen($textEn) / 2, //评估宽度
            'destH' => $this->opts['wm_fontSize'],
            'pos' => $this->opts['wm_fontAlign']
        ));
        
        $x = $pos['x'];
        $y = $pos['y'];

        MagickResetIterator($this->srcIm);
        do {
            MagickAnnotateImage($this->srcIm, $ndw, $x, $y, 0, $textEn); //写上文字水印
        } while (MagickNextImage($this->srcIm));
        MagickWriteImages($this->srcIm, $this->srcFile, true);
        DestroyMagickWand($this->srcIm);
        ClearPixelWand($fontColor);
        ClearDrawingWand($ndw);
        DestroyPixelWand($fontColor);
        DestroyDrawingWand($ndw);
        return true;
    }
    
    /**
     * 缩略图函数
     * @param array $opts
     * @return string 生成缩略图后的文件名
     */
    public function resize($opts)
    {
        if (!$this->init($opts['srcFile']))
            return false;
        
        $maxWidth  = $opts['maxWidth'];
        $maxHeight = $opts['maxHeight'];
        if($this->srcWidth < $maxWidth && $this->srcHeight < $maxHeight)
        {
            @copy($opts['srcFile'], $opts['destFile']);
            return $opts['destFile'];
        }

        $wh = Image::getResizeWH($maxWidth, $maxHeight, $this->srcWidth, $this->srcHeight);
        MagickResetIterator($this->srcIm);
        do {

            if ($this->opts['roundCorners']
            && MagickGetImageWidth($this->srcIm) == $this->srcWidth 
            && MagickGetImageHeight($this->srcIm) == $this->srcHeight)
                MagickRoundCorners($this->srcIm, $this->opts['roundCorners_x'], $this->opts['roundCorners_y']);//$this->roundedCorner();

            MagickThumbnailImage($this->srcIm, $wh['width'], $wh['height']);

        } while (MagickNextImage($this->srcIm));

        MagickWriteImages($this->srcIm, $opts['destFile'], true);
        DestroyMagickWand($this->srcIm);
        
        return $opts['destFile'];
    }
    
    /**
     * 圆角处理
     */
    private function roundedCorner()
    {
        $im2 = NewMagickWand();
        MagickReadImage($im2, APP_PATH . '/assets/rounded_corner.png');
        
        $rotate = array(
            0,
            90,
            180,
            270
        );
        $pixel  = NewPixelWand('#ffffff');
        //循环合并到图片四个角
        foreach ($rotate as $r) {
            if ($r)
                MagickRotateImage($this->srcIm, $pixel, $r);
            MagickCompositeImage($this->srcIm, $im2, MW_OverCompositeOp, 0, 0);
        }
        
        MagickRotateImage($this->srcIm, $pixel, 180);

    }
    
} //end class