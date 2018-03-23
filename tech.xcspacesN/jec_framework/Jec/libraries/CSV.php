<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * CSV相关操作类
 */

class MyCsv
{
    private $data = array();
    private $encoding = '';

    public function __construct()
    {
        if(function_exists('ini_set'))
            ini_set('memory_limit','1024M');
    }

    public function setEncoding($encoding)
    {
        $this->encoding = strtolower($encoding);
    }

    /**
     * 清空csv文件内容
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * 添加一行数据
     * @param array $row
     * @return null
     */
    public function addRow($row)
    {
        if(!$row)return;
        $this->data[] = $row;
    }

    /**
     * 一次性添加所有行
     * @param array $data
     */
    public function addAll($data)
    {
        $this->data = $data;
    }

    /**
     * 告诉浏览器下载csv文件
     * @param string $filename
     */
    public function download($filename)
    {
        Net::sendDownloadHeader($filename, 'text/csv');
        $fp = fopen('php://output', 'w');
        foreach($this->data as $row)
        {
            if($this->encoding && $this->encoding != 'utf-8')
            {
                foreach($row as &$Pval)
                {
                    $Pval = iconv('utf-8', $this->encoding, $Pval);
                }
            }
            
            //解决乱码问题
            // if($key == 0) $row[0] = "\xEF\xBB\xBF" . $row[0];

            fputcsv($fp, $row);
        }
        fclose($fp);
        $this->clear();
        exit;
    }
}