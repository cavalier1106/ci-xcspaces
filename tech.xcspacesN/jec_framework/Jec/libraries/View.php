<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 模板处理类
 */
class View extends Controller
{
    public static $varPattern = '(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\+\*\-/"\'\[\]\$\x7f-\xff]*)';
    private $_tplVar = array();
    private $_current_tpl_file = '';
    public $left_delimiter = '<{';
    public $right_delimiter = '}>';

    /**
     * 直接输出模板内容
     * @param string $tplFile 可选，默认为当前model/views/action.html
     * @throws JecException
     */
    public function display($tplFile='')
    {
        //让模板可以直接读取配置信息，不能去掉
        global $CONFIG;
        if(!$tplFile)
        {
            $module = str_replace('_','/',$this->getModuleName());
            $lastDS = strrpos($module,'/');
            $tplFile = substr($module,0, $lastDS).'/Views/'.substr($module, $lastDS+1).'.html';
        }
        $compiledFile = $this->compile($tplFile);
        //提取变量
        extract($this->_tplVar);
        require $compiledFile;
    }

    /**
     * 编译一个模板文件
     * @param $tplFile 相对路径
     * @return string 编译后的内容
     * @throws JecException
     */
    public function compile($tplFile)
    {
        if(!$tplFile)
            throw new JecException('请指定模板文件！');

        $path = VAR_PATH . '/templates_c/';
        _mkdir($path);
        $compiledFile = $path . str_replace('/','.',$tplFile) . '.php';

        $tplFile     = MOD_PATH . DS . $tplFile;
        $this->_current_tpl_file = $tplFile;
        if (!is_file($tplFile))
            throw new JecException("$tplFile 模板文件不存在！");
        //模板生成规则
        $last_edit_time = is_file($compiledFile) ? filemtime($compiledFile) : 0;
        $file_edit_time = filemtime($tplFile);
        if($file_edit_time != $last_edit_time){
            $rs1 = file_put_contents($compiledFile, $this->_parseTpl($tplFile));
            $rs2 = touch($compiledFile, $file_edit_time);
            if(!$rs1 || !$rs2)
                throw new JecException("无法写入模板文件！");
        }
        return $compiledFile;
    }

    /**
     * 设置模板变量
     * @static
     * @param array|string $key
     * @param null|mixed $val
     * @return void
     */
    public function assign($key, $val=null)
    {
        if(is_array($key))
        {
            foreach($key as $k=>$v)
            {
                $this->_tplVar[$k] = $v;
            }
        }else{
            $this->_tplVar[$key] = $val;
        }
    }

    /**
     * 获取通过assign方法指派过的值，失败将返回false
     * @param $key
     * @return bool|mixed 已经指派的值
     */
    public function getAssignVar($key)
    {
        if(!isset($this->_tplVar[$key]))
            return false;

        return $this->_tplVar[$key];
    }

    /**
     * 直接保存输出内容到文件,当目录不存在时,自动递归创建
     * @param $destFile 新文件路径
     * @param string $tplFile 模板文件
     * @return boolean
     */
    public function saveto($destFile, $tplFile='')
    {
        ob_start();
        $this->display($tplFile);
        $content = ob_get_contents();
        ob_end_clean();
        _mkdir(dirname($destFile));
        return file_put_contents($destFile, $content);
    }
    
    /**
     * 解析模板文件
     * @param string $file
     * @return string 解析后的php模板数据
     */
    private function _parseTpl($file)
    {
        $tplData = file_get_contents($file);
        $tplData = preg_replace('#\{' . View::$varPattern . '\}#s', '<?php echo \1 ; ?>', $tplData); //处理变量
        //include file内容里面的变量不能用{$var}这样的格式，要用$var
        $tplData = preg_replace_callback('/'.$this->left_delimiter.'include\s+file=["\'](.+?)["\']\s*'.$this->right_delimiter.'/', array($this, '_doInclude'), $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'if (.+?)'.$this->right_delimiter.'/', '<?php if (\1): ?>', $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'else'.$this->right_delimiter.'/', "<?php else: ?>", $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'elseif (.+?)'.$this->right_delimiter.'/', '<?php elseif (\1): ?>', $tplData);
        $tplData = preg_replace('#'.$this->left_delimiter.'/if'.$this->right_delimiter.'#', "<?php endif; ?>", $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'foreach (.+?)'.$this->right_delimiter.'/', "<?php foreach(\\1): ?>", $tplData);
        $tplData = preg_replace('#'.$this->left_delimiter.'/foreach'.$this->right_delimiter.'#', "<?php endforeach; ?>", $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'for (.+?)'.$this->right_delimiter.'/', '<?php for(\1): ?>', $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'\/for'.$this->right_delimiter.'/', '<?php endfor; ?>', $tplData);
        $tplData = preg_replace('/'.$this->left_delimiter.'echo (.+?)'.$this->right_delimiter.'/', '<?php echo \1 ; ?>', $tplData);
        //<{php}> echo 'x'; <{/php}>
        $tplData = preg_replace('/'
            .$this->left_delimiter.'php'.$this->right_delimiter
            .'(.+?)'
            .$this->left_delimiter.'\/php'.$this->right_delimiter
            .'/', '<?php \1 ; ?>', $tplData);
        return $tplData;
    }
    
    private function _doInclude($match)
    {
        $incFile = trim($match[1]);
        return '<?php require $this->compile(\''.$incFile.'\'); ?>';
    }

}
