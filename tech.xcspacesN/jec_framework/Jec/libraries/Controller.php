<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * 控制器处理类
 */
 
class Controller
{
    private static $moduleKey = 'm';

    /**
     * @static
     * 获取模块名称
     * @return bool|string
     */
    public static function getModuleName()
    {
        return (string)Jec::getKeyWord(self::$moduleKey,'GET');
    }

    /**
     * @static
     * 设置模块名称
     * @param $name
     */
    public static function setModuleName($name)
    {
        $_GET[self::$moduleKey] = $name;
    }

    /**
     * 判断是否是一个合法的模块名称
     * @param null $name
     * @return bool
     */
    public function isValidModuleName($name=null)
    {
        return (bool)preg_match('/^[A-Z]\w+(_[A-Z]\w+)*$/', $name===null ? self::getModuleName() : $name);
    }

    /**
     * 执行一个模块,成功则返回该模块的类,否则异常退出
     * @return mixed
     * @throws JecException
     */
    public function startModule()
    {
        $module = self::getModuleName();

        if(!$this->isValidModuleName($module))
            throw new JecException('无法尝试执行一个非标准模块：'.$module);

        if(!is_file(MOD_PATH.DS.str_replace('_','/',$module).'.php'))
            throw new JecException('模块 '.$module.' 不存在！');

        return new $module();
    }


}