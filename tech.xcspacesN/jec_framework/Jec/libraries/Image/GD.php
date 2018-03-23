<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * GD图像处理驱动
 */

class Image_GD
{
    public $opts = array();
    
    public function __construct($opts)
    {
        $this->opts = $opts;
    }

    /**
     * 缩略图函数(转自：ThinkPHP)
     * @param array $opts
     * @return string 缩放后的文件路径
     */
    public function resize($opts)
    {
        $image = $opts['srcFile'];
        // 获取原图信息
        $info  = $this->_getImageInfo($image);
        if ($info === false)
            return false;
        $srcWidth  = $info['width'];
        $srcHeight = $info['height'];
        $pathinfo  = pathinfo($image);
        $type      = $pathinfo['extension'];
        //$type = $info['type'];
        $type      = strtolower($type);
        
        unset($info);
        $scale = min($opts['maxWidth'] / $srcWidth, $opts['maxHeight'] / $srcHeight); // 计算缩放比例
        
        // 缩略图尺寸
        $width  = (int) ($srcWidth * $scale);
        $height = (int) ($srcHeight * $scale);
        
        // 载入原图
        $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
        $srcImg    = $createFun($image);
        
        //创建缩略图
        if ($type != 'gif' && function_exists('imagecreatetruecolor'))
            $thumbImg = imagecreatetruecolor($width, $height);
        else
            $thumbImg = imagecreate($width, $height);
        
        // 复制图片
        if (function_exists("ImageCopyResampled"))
            ImageCopyResampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
        else
            ImageCopyResized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
        if ('gif' == $type || 'png' == $type) {
            //imagealphablending($thumbImg, false);//取消默认的混色模式
            //imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
            $background_color = imagecolorallocatealpha($thumbImg, 255, 255, 255, 127); //  指派一个绿色
            imagecolortransparent($thumbImg, $background_color); //  设置为透明色，若注释掉该行则输出绿色的图
        }
        
        // 对jpeg图形设置隔行扫描
        if ('jpg' == $type || 'jpeg' == $type)
            imageinterlace($thumbImg, 1);
        
        if($opts['roundCorners'])
            $thumbImg = $this -> roundCorners($thumbImg);
        // 生成图片
        $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
        
        $imageFun($thumbImg, $opts['destFile']);
        ImageDestroy($thumbImg);
        ImageDestroy($srcImg);
        return $opts['destFile'];
        
    }
    
    /**
     * 圆角处理
     * @param resource $im
     * @return resource
     */
    private function roundCorners($im)
    {
        //圆角PNG图片
        $corner_source = imageCreateFromPNG(APP_PATH . '/assets/rounded_corner.png');
        $corner_width = imagesX($corner_source);  
        $corner_height = imagesY($corner_source);  
        $rotate = array(0,90,180,270);
        //循环合并到图片四个角
        foreach($rotate as $r)
        {
            if($r)
                $im = imageRotate($im, $r, 0);
            imageCopyMerge($im, $corner_source, 0, 0, 0, 0, $corner_width, $corner_height, 100);
        }
        //旋转回来
        return imageRotate($im, 180, 0);
    }
    
    private function _getImageInfo($img)
    {
        $imageInfo = getimagesize($img);
        if ($imageInfo === false)
            return false;
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
        $imageSize = filesize($img);
        $info      = array(
            "width" => $imageInfo[0],
            "height" => $imageInfo[1],
            "type" => $imageType,
            "size" => $imageSize,
            "mime" => $imageInfo['mime']
        );
        return $info;
    }
    
    /**
     * 水印函数,没有指定水印图片,则使用文字水印
     * @param string $srcFile 源文件地址
     * @return null
     */
    function watermark($srcFile)
    {
        $srcInfo   = getimagesize($srcFile);

        switch ($srcInfo[2]) {
            case 1:
                if (!function_exists("imagecreatefromgif"))
                    return;
                $srcFile_img = imagecreatefromgif($srcFile);
                break;
            case 2:
                if (!function_exists("imagecreatefromjpeg"))
                    return;
                $srcFile_img = imagecreatefromjpeg($srcFile);
                break;
            case 3:
                if (!function_exists("imagecreatefrompng"))
                    return;
                $srcFile_img = imagecreatefrompng($srcFile);
                break;
            case 6:
                if (!function_exists("imagewbmp"))
                    return;
                $srcFile_img = imagecreatefromwbmp($srcFile);
                break;
            default:
                return;
        }
        $w_img = $this->opts['wm_img'];
        //读取水印图片
        if (!empty($w_img) && file_exists($w_img)) {
            $ifWaterImage = 1;
            
            $water_info = getimagesize($w_img);
            $width      = $water_info[0];
            $height     = $water_info[1];
            switch ($water_info[2]) {
                case 1:
                    if (!function_exists("imagecreatefromgif"))
                        return;
                    $water_img = imagecreatefromgif($w_img);
                    break;
                case 2:
                    if (!function_exists("imagecreatefromjpeg"))
                        return;
                    $water_img = imagecreatefromjpeg($w_img);
                    break;
                case 3:
                    if (!function_exists("imagecreatefrompng"))
                        return;
                    $water_img = imagecreatefrompng($w_img);
                    break;
                case 6:
                    if (!function_exists("imagecreatefromwbmp"))
                        return;
                    $water_img = imagecreatefromwbmp($w_img);
                    break;
                default:
                    return;
            }
        } else {
            $ifWaterImage = 0;
            $ifttf        = 1;
            @$temp = imagettfbbox($this->opts['wm_fontSize'], 0, $this->opts['wm_font'], $this->opts['wm_text']);
            $width  = $temp[2] - $temp[6];
            $height = $temp[3] - $temp[7];
            unset($temp);
            if (empty($width) && empty($height)) {
                $width  = strlen($this->opts['wm_text']) * 10;
                $height = 20;
                $ifttf  = 0;
            }
        }
        //水印位置
        $pos = Image::getWatermarkPos($this->opts['wm_fontAlign']);
        $wX  = $pos['x'];
        $wY  = $pos['y'];
        
        $w_color = $this->opts['wm_fontColor'];
        
        //写入水印
        imagealphablending($srcFile_img, true);
        if ($ifWaterImage) {
            imagecopymerge($srcFile_img, $water_img, $wX, $wY, 0, 0, $width, $height, $this->opts['wm_fontAlpha'] * 100);
        } else {
            if (!empty($w_color) && (strlen($w_color) == 7)) {
                $R = hexdec(substr($w_color, 1, 2));
                $G = hexdec(substr($w_color, 3, 2));
                $B = hexdec(substr($w_color, 5));
            } else {
                return;
            }
            if ($ifttf)
                imagettftext($srcFile_img, $this->opts['wm_fontSize'], 0, $wX, $wY, imagecolorallocate($srcFile_img, $R, $G, $B), $this->opts['wm_font'], $this->opts['wm_text']);
            else
                imagestring($srcFile_img, $this->opts['wm_fontSize'], $wX, $wY, $this->opts['wm_text'], imagecolorallocate($srcFile_img, $R, $G, $B));
        }
        //保存结果
        switch ($srcInfo[2]) {
            case 1:
                if (function_exists("imagegif"))
                    imagegif($srcFile_img, $srcFile);
                break;
            case 2:
                if (function_exists("imagejpeg"))
                    imagejpeg($srcFile_img, $srcFile);
                break;
            case 3:
                if (function_exists("imagepng"))
                    imagepng($srcFile_img, $srcFile);
                break;
            case 6:
                if (function_exists("imagewbmp"))
                    imagewbmp($srcFile_img, $srcFile);
                break;
            default:
                return;
        }
        if (isset($water_info))
            unset($water_info);
        if (isset($water_img))
            imagedestroy($water_img);
        unset($srcInfo);
        imagedestroy($srcFile_img);
    }
}