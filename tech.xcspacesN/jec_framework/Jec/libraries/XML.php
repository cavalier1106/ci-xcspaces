<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * XML文件处理类
 */
class XML
{
    /**
     * @static
     * @param string $filename 完整的XML路径
     * @return array
     */
    public static function read($filename)
    {
        $array  = array();
        $xml    = file_get_contents($filename);
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        $index  = array();
        $values = array();
        xml_parse_into_struct($parser, $xml, $values, $index);
        xml_parser_free($parser);
        $i            = 0;
        $name         = $values[$i]['tag'];
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
        $array[$name] = self::_struct_to_array($values, $i);
        return $array;
    }
    
    private static function _struct_to_array($values, &$i)
    {
        $child = array();
        if (isset($values[$i]['value']))
            array_push($child, $values[$i]['value']);
        
        while ($i++ < count($values)) {
            switch ($values[$i]['type']) {
                case 'cdata':
                    array_push($child, $values[$i]['value']);
                    break;
                
                case 'complete':
                    $name = $values[$i]['tag'];
                    if (!empty($name)) {
                        $child[$name] = isset($values[$i]['value']) ? ($values[$i]['value']) : '';
                        
                        if (isset($values[$i]['attributes'])) {
                            $child[$name] = $values[$i]['attributes'];
                        }
                    }
                    break;
                
                case 'open':
                    $name                = $values[$i]['tag'];
                    $size                = isset($child[$name]) ? count($child[$name]) : 0;
                    $child[$name][$size] = self::_struct_to_array($values, $i);
                    break;
                
                case 'close':
                    return $child;
                    break;
            }
        }
        return $child;
    }
    
    /**
     * @param string $file 路径
     * @param array $data 数组
     * @return boolean
     */
    public static function write($file, $data)
    {
        $doc = '';
        $doc .= '<?xml version="1.0" encoding="utf-8"?>';
        $doc .= self::_array2xml($data);
        return file_put_contents($file, $doc);
    }
    
    private static function _array2xml($arr)
    {
        $doc = '';
        foreach ($arr as $tag => $value) {
            if (is_array($value))
                $value = self::_array2xml($value);
            
            
            $doc .= "\r\n<$tag><![CDATA[$value]]></$tag>";
            
        }
        return $doc;
    }
}
