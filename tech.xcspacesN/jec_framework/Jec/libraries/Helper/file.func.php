<?php

/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 * 文件操作函数库
 */
/**
 * 创建一个PHP文件
 * @param string $file 路径
 * @param mixed $var 变量
 * @return boolean 是否写入成功
 */
function makePhp($file, $var)
{
    $data = '';
    if (!$file)
        return false;
    $data .= "<?php\n//this file create by Jec framework " . date('Y-m-d H:i:s') .
        "\nreturn " . var_export($var, true) . ';';
    return file_put_contents($file, $data);
}

/**
 * 删除一个目录及里面所有文件
 * @param string $path 路径
 * @param bool $del_dir  是否将本目录也删除
 * @param int $level 注,该参数不能人工干预,请使用默认值,内部判断是否为目录传参
 * @return null
 */
function _rmdir($path, $del_dir = false, $level = 0)
{
    // Trim the trailing slash
    $path = preg_replace("|^(.+?)/*$|", "\\1", $path);
    if (!file_exists($path))
        return;
    $current_dir = scandir($path);
    foreach ($current_dir as $filename)
    {
        if ($filename != "." && $filename != "..")
            is_dir($path . DS . $filename) ? _rmdir($path . DS . $filename,
                $del_dir, $level + 1) : unlink($path . DS . $filename);
    }
    if ($del_dir == true && $level > 0)
        @rmdir($path);
}

/**
 * 生成一个随机文件名
 *
 * @param string $ext 指定扩展名
 * @return string
 */
function getRandFileName($ext)
{
    return time() . rand(100, 999) . '.' . $ext;
}

/**
 * 判断是否为一个有效的完整路径，匹配d:\ \\192.0.0.1这类格式
 * @param string $fileName
 * @return bool
 */
function isValidFile($fileName)
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        return (bool)preg_match('/(([a-zA-Z]:)|(\\\{2}\w+))(\\\(.*))+\.\w{2,6}/i',
            $fileName);
    else
        return (bool)preg_match('#^/[^/]+((/[^/]+)*)$#', $fileName);
}


/**
 * 获取$path下的所有文件
 * @param string $path
 * @param bool $inc_sub 是否包含子目录
 * @param int $order  scandir的第二个参数,默认按字母升序排列。如果设为 1 按字母降序排列
 * @return array
 */
function getFilesOnly($path, $inc_sub = true, $order = 0)
{
    $folders = array();
    if (!is_dir($path))
        return $folders;
    $files = scandir($path, $order);
    foreach ($files as $folder)
    {
        if ($folder != '.' && $folder != '..') {
            if ($inc_sub && is_dir($path . DS . $folder)) {
                $tmp = getFilesOnly($path . DS . $folder);
                foreach ($tmp as $val)
                    $folders[] = $val;
            }
            if (is_file($path . DS . $folder))
                $folders[] = $path . DS . $folder;
        }
    }
    return $folders;
} //end func

/**
 * 获取$path下的所有文件夹
 * @param string $path
 * @param bool $inc_sub 是否包含子目录
 * @param int $order  scandir的第二个参数,默认按字母升序排列。如果设为 1 按字母降序排列
 * @return array
 */
function getFolderOnly($path, $inc_sub = true, $order = 0)
{
    $folders = array();
    if (!is_dir($path))
        return $folders;
    $files = scandir($path, $order);
    foreach ($files as $folder)
    {
        if ($inc_sub && is_dir($path . DS . $folder)) {
            $tmp = getFolderOnly($path . DS . $folder);
            foreach ($tmp as $val)
                $folders[] = $val;
        }
        if (is_dir($path . DS . $folder) && $folder != '.' && $folder != '..')
            $folders[] = $folder;
    }
    return $folders;
} //end func

/**
 * 获取$path下的所有文件夹和文件
 * @param string $path
 * @param bool $inc_sub 是否包含子目录
 * @param int $order  scandir的第二个参数,默认按字母升序排列。如果设为 1 按字母降序排列
 * @return array
 */
function getDirList($path, $inc_sub = true, $order = 0)
{
    $folders = array();
    if (!is_dir($path))
        return $folders;
    $files = scandir($path, $order);
    foreach ($files as $folder)
    {
        if ($inc_sub && is_dir($path . DS . $folder)) {
            $tmp = getDirList($path . DS . $folder);
            foreach ($tmp as $val)
                $folders[] = $val;
        }
        if ($folder != '.' && $folder != '..')
            $folders[] = $folder;
    }
    return $folders;
} //end func

/**
 * 递归创建所有不存在的目录
 * @param string $path 目标路径
 * @param int $mode 权限，安全方案：注意:文件夹必须带有x权限才能有读取权限,文件带有x权限则有执行权限
 * @return bool
 */
function _mkdir($path, $mode = 0744)
{
    if (is_dir($path))
        return true;
    //bool mkdir ( string $pathname [, int $mode = 0777 [, bool $recursive = false [, resource $context ]]] )
    return mkdir($path, $mode, true);
}

/**
 * 获得文件大小为8kb这样的格式
 * @param int $bytes 文件大小整数
 * @return string 返回格式如: 1 Kb or 1 MB..
 */
function formatBytes($bytes)
{
    //注：使用if是最快速的
    if ($bytes < 1024) return $bytes . ' B';
    elseif ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
    elseif ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
    elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2) . ' GB';
    else return round($bytes / 1099511627776, 2) . ' TB';
}

/**
 * 根据文件大小名称还原文件大小为整数
 * @param string $fsize 如 856 KB
 * @param array $unim 大小名称样式，不区分大小写，按array(多少个1024次方=>名称)
 * @return int $filesize 返回字节整数
 */
function getFileSizeFromName($fsize, $unim = array(0 => "B", 1 => "KB", 2 => "M", 3 => "G", 4 => "TB", 5 => "PB"))
{
    if (!$fsize)
        return 0;
    foreach ($unim as $n => $u)
    {
        if (preg_match("`^([\\d\\.]+)\\s*{$u}$`i", $fsize, $match)) {
            $srcFilesize = (float)$match[1];
            return round($srcFilesize * pow(1024, $n));
        }
    }
    return 0;
}

/**
 * 获取一个标准的文件路径
 * @param string $path 目录
 * @return string 标准目录
 */
function getPath($path)
{
    if (empty($path))
        return '';
    $path_dir = str_replace(array('/', "\\"), DS, $path);
    $path_dir = trim($path_dir, DS);
    return $path_dir;
}
