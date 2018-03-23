<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * HTML文档处理类
 */

class Html
{
    private static $switchStyle = 0;

    /**
     * @static
     * 在两个不同的样式之间切换,可用来做表格不同行不同颜色
     * @param string $s1 样式1,可以是一个css className或css style
     * @param string $s2
     * @return string $s1或$s2
     */
    public static function switchStyle($s1, $s2)
    {
        if(self::$switchStyle == 0)
        {
            self::$switchStyle = 1;
            return $s1;
        }else{
            self::$switchStyle = 0;
            return $s2;
        }
    }
    
    /**
     * 将内容中的表格转换成数组
     * @param string $table
     * @return array
     */
    public static function getTableAsArray($table)
    {
        $table = preg_replace("#<table[^>]*?>#si", "", $table);
        $table = preg_replace("#<tr[^>]*?>#si", "", $table);
        $table = preg_replace("#<td[^>]*?>#si", "", $table);
        $table = str_replace("</tr>", "{tr}", $table);
        $table = str_replace("</td>", "{td}", $table);
        //去掉 HTML 标记 
        $table = preg_replace('#<[\/\!]*?[^<>]*?>#si', "", $table);
        //去掉空白字符 
        $table = preg_replace('#([\r\n])[\s]+#', "", $table);
        $table = str_replace(" ", "", $table);
        $table = str_replace(" ", "", $table);
        
        $table = explode('{tr}', $table);
        array_pop($table);
        $td_array = array();
        foreach ($table as $tr) {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td_array[] = $td;
        }
        return $td_array;
    }
    
    /**
     * 获取html字符串中的所有A标签链接
     * @param string $code html代码
     * @return array
     */
    public static function getAllUrl($code)
    {
        preg_match_all('/<a\s+href=["|\']?([^>"\' ]+)["|\']?\s*[^>]*>([^>]+)<\/a>/i', $code, $arr);
        $rt = array();
        foreach ($arr[1] as $key => $url)
            $rt[] = array(
                'name' => $arr[2][$key],
                'url' => $url
            );
        
        return $rt;
    }

    /**
     * @static
     * 为input元素设置checked属性
     * @param bool $c 判断条件
     * @return string 为真则返回checked="checked" 否则返回空
     */
    public static function setChecked($c)
    {
        return $c ? 'checked="checked"' : '';
    }

    /**
     * @static
     * 为select元素设置selected属性
     * @param bool $c 判断条件
     * @return string 为真则返回selected="selected" 否则返回空
     */
    public static function setSelected($c)
    {
        return $c ? 'selected="selected"' : '';
    }

    /**
     * @static
     * 为HTML元素设置类名
     * @param string $className css的类名称
     * @param bool $c 判断条件
     * @return string 为真则返回 class="$className" 否则返回空
     */
    public static function setClass($className, $c)
    {
        return $c ? " class=\"{$className}\"" : '';
    }

    /**
     * @static
     * 创建一个select元素的html代码
     * @param string $name 设置name属性
     * @param array $data option元素列表内容 格式:array(value=>text,,)
     * @param null|mixed $defaultValue 默认值
     * @param string $attrStr 附加属性,必须是正确的html代码
     * @return string
     */
    public static function getSelect($name, $data, $defaultValue=null, $attrStr='')
    {
        $sel = '<select name="'.$name.'" '.$attrStr.'>';
        if($defaultValue !== null && !is_array($defaultValue))
            $defaultValue = array($defaultValue);
        foreach($data as $value=>$text)
        {
            if(is_array($text))
            {
                $sel .= '<optgroup label="'.$value.'">';
                foreach($text as $val=>$name)
                {
                    $selected = '';
                    if(null !== $defaultValue && in_array($val, $defaultValue))
                        $selected = ' selected="selected"';
                    $sel .= '<option value="'.$val.'"'.$selected.'>'.$name.'</option>';
                }
            }else{
                $selected = '';
                if(null !== $defaultValue && in_array($value, $defaultValue))
                    $selected = ' selected="selected"';
                $sel .= '<option value="'.$value.'"'.$selected.'>'.$text.'</option>';
            }
        }
        $sel .= '</select>';
        return $sel;
    }

    /**
     * @static
     * 创建一段单选表单HTML代码
     * @param string $name 设置name属性
     * @param array $data option元素列表内容 格式:array(value=>text,,)
     * @param null|mixed $defaultValue 默认值
     * @param string $attrStr 附加属性,必须是正确的html代码
     * @return string
     */
    public static function getRadio($name, $data, $defaultValue=null, $attrStr='')
    {
        return self::makeInput('radio', $name, $data, array($defaultValue), $attrStr);
    }

    /**
     * @static
     * 创建一段多选表单HTML代码
     * @param string $name 设置name属性
     * @param array $data option元素列表内容 格式:array(value=>text,,)
     * @param array $defaultValue 默认值
     * @param string $attrStr 附加属性,必须是正确的html代码
     * @return string
     */
    public static function getCheckbox($name, $data, $defaultValue=null, $attrStr='')
    {
        return self::makeInput('checkbox', $name, $data, $defaultValue, $attrStr);
    }

    private static $autoIncrement = 0;
    private static function makeInput($type, $name, $data, $defaultValue=array(), $attrStr='')
    {
        $html = array();
        foreach($data as $value=>$text)
        {
            $checked = $defaultValue!==null && in_array($value, $defaultValue) ? 'checked="checked"' : '';
            $id = "jec_input_autoId_".(++self::$autoIncrement);
            $html[] = ' <input id="'.$id.'" type="'.$type.'" name="'.$name.'" value="'.$value.'" '.$attrStr.' '.$checked.' /> <label for="'.$id.'">'.$text.'</label>';
        }
        return implode(' &nbsp;', $html);
    }

}