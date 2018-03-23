<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 文件上传类
 */
class Upload
{
    private $file_type = array('image'); //文件类型 video, audio, image详见_getFileExts函数
    private $file_exts = array(); //如果$file_type里没有，则查找这里的扩展名
    private $limit_exts = array('php', 'php3', 'phtml', 'php4', 'asp', 'html', 'htm', 'js', 'vbs', 'bat');
    private $max_size = 10485760; //0为不限制，单位b 10M = 10485760b
    private $rename = 1; //是否重命名
    private $filename_suffix = null; //文件名后缀
    private $savePath = ''; //文件保存绝对路径，不指定则调用配置文件中的路径

    private $error = array(); //错误集
    
    
    public function __construct($params = array())
    {
        foreach ($params as $key => $val)
            $this->$key = $val;

    }
    
    /**
     * 执行上传
     * @param $input_name 上传表单区域的名称
     * @return array() , bool 返回上传信息数组或false
     * @return array (
     *             'name' => 'cpu.jpg',
     *             'ext' => 'jpg',
     *             'size' => 338182,
     *             'size2' => '330.26 KB',
     *             'type' => 'image/pjpeg',
     *             'url' => '/static/soft/cpu.jpg',
     *             'path' => 'D:\wwwroot\com\site\static\soft\cpu.jpg',
     *            )
     */
    public function save($input_name)
    {
        $result = array();
        
        if (!isset($_FILES[$input_name]) || empty($_FILES[$input_name]['name']))
            return false;
        
        $files =& $_FILES[$input_name]; //这样引用更有效率
 
        if (is_array($files['name']))
        {
            //上传多个文件
            $file = array();
            foreach ($files['name'] as $key => $val)
            {
                if (empty($val))
                    continue;
                if($files['error'][$key])
                {
                    $this -> error[] = $this -> codeToMessage($files['error'][$key]);
                    return false;
                }
                    
                $file['tmp_name'] = $files['tmp_name'][$key];
                $file['name']     = $files['name'][$key];
                $file['type']     = $files['type'][$key];
                $file['size']     = $files['size'][$key];
                //$file['error'] = $files['error'][$key];
                $rt = $this->_upload($file);
                if(!$rt)return false;
                $result[$key] = $rt;
            }
            //end multi-file upload
        }
        else
        {
            if ($files['error'])
            {
                $this -> error[] = $this -> codeToMessage($files['error']);
                return false;
            }
            $result = $this->_upload($files);
        }
        
        return $result ? $result : false;
        
    }
    
    /**
     * 处理上传文件
     * @param array $file_info
     * @return array
     */
    private function _upload($file_info)
    {
        $result = array();
        $result['src_name'] = $file_info['name'];
        $name_ext = $this->_getFilename($file_info);
        if(!$name_ext)return false;
        $result['name']  = $name_ext['name'];
        $result['ext']   = $name_ext['ext'];
        $result['size']  = $file_info['size'];
        $result['size2'] = $this->_getFileSize($file_info['size']);
        if(!$result['size2'])return false;
        $result['type']  = $file_info['type'];
        
        $path              = $this->_getFilePath($name_ext['name']);
        $result['url']     = substr($path['path'], strlen(dirname($this->savePath)));
        $result['url']     = str_replace('\\', '/', $result['url']);
        $result['path']    = $path['path']; //完整路径，即包含文件名
        
        if (!move_uploaded_file($file_info['tmp_name'], $path['path']))
        {
            $this -> error[] = '无法移动上传文件！';
            return false;
        }

        return $result;
    }

    /**
     * 返回错误内容
     * @return null|string
     */
    public function getError()
    {
        return $this -> error ? implode('', (array)$this -> error) : null;
    }
    
    private function _getFilePath($name)
    {
        global $CONFIG;
        $path      = $this->savePath ? $this->savePath : $CONFIG['upload_path'];
        $rightChar = substr($path, -1, 1);
        if ($rightChar != "\\" && $rightChar != '/')
            $path .= DS;
        
        $this->savePath = $path;
        
        _mkdir($path);

        return array(
            'path' => $path . $name,
        );
    }
    
    /**
     * 取得上传后的文件名，不包含路径
     * @param array $fileInfo 上传区域$_FILES信息
     * @return bool|array (name, ext)
     */
    private function _getFilename($fileInfo)
    {
        $name = $fileInfo['name'];
        $name = trim($name);
        $paras = explode('.', $name);
        if (!is_array($paras) || !$paras)
        {
            $this->error[] = '无效的上传文件名：'.$name.'！';
            return false;
        }
        
        $ext = array_pop($paras);
        $name = implode('.', $paras);
        
        if(!$ext || !$name)
        {
            $this->error[] = '无效的上传文件名或文件：'.$name.'.'.$ext.'！';
            return false;
        }
        
        $ext = strtolower($ext);
        if(!$this->_checkFileType($ext))
            return false;

        $new_name = $this -> rename ? date('YmdHis').'_'.rand(10000, 99999) : $name;
        if($this->filename_suffix)
            $new_name .= $this->filename_suffix;

        return array(
            'name' => $new_name.'.'.$ext,
            'ext' => $ext
        );
        
    }
    
    private function _getFileSize($size)
    {
        $max_size    = $this->max_size;
        $size_format = formatBytes($size);
        if (!$max_size)
            return $size_format;
        
        if ($size > $max_size)
        {
            $this -> error[] = "文件大小不能超过：" . formatBytes($max_size).'！';
            return false;
        }
        
        return $size_format;
    }
    
    private function _checkFileType($ext)
    {
        if (in_array($ext, $this->file_exts))
            return true;
        
        foreach ($this->file_type as $val)
        {
            $exts = (array) self::_getFileExts($val);
            if (in_array($ext, $exts))
                return true;
        }
        
        if (in_array($ext, $this->limit_exts))
        {
            $this->error[] = "系统限制上传文件类型：{$ext}！";
            return false;
        }
        
        return true;
    }

    private static function _getFileExts($name)
    {
        switch ($name)
        {
            case 'video':
                return array('mpg', 'mpeg', 'avi', 'rm', 'rmvb', 'mov', 'wmv', 'asf', 'dat'); //.asx, .wvx, .mpe, .mpa

            case 'audio':
                return array('mp3', 'wma', 'rm', 'ram', 'wav', 'mid', 'midi', 'rmi', 'm3u',
                    'ogg', 'ape', 'cda'); //.au, .aiff, .aif, .aifc, .669, .wax, .snd

            case 'image':
                return array('jpeg', 'jpg', 'gif', 'bmp', 'png', 'ico', 'icl', 'psd', 'tif',
                    'cr2', 'crw', 'cur', 'ani');

            case 'app':
                return array('exe', 'msi', 'bat', 'dll');

            case 'flash':
                return array('fla', 'swf', 'flv');

            case 'text':
                return array('txt', 'rtf', 'doc', 'chm', 'ini', 'log');

            case 'compress':
                return array('zip', 'rar', 'cab', 'ace', 'z', 'arc', 'arj', 'lzh', 'tar', 'uue',
                    'gzip');
                    
            case 'mobile':
                return array('jar','jad','sis','sisx','prc','pxl');

            case 'iso':
                return array('iso', 'bin', 'cif', 'nrg', 'vcd', 'fcd', 'img', 'c2d', 'tao',
                    'dao', 'vhd');
                
        }
        return array();
    }
    
    private function codeToMessage($code) 
    { 
        switch ($code) { 
            case UPLOAD_ERR_INI_SIZE: 
                $message = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $message = '上传文件的大小超过了您阅读器的表单内容大小限制！'; 
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $message = '文件只有部分被上传！';
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $message = '没有文件被上传！';
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $message = '找不到临时文件夹！';
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $message = '文件写入失败！';
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $message = '上传的文件受扩展名限制！'; 
                break; 

            default: 
                $message = '未知上传错误！';
                break; 
        } 
        return $message; 
    } 
}
