<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 *
 * 多语言化处理
 *
 * 初始化后可以使用 _('str') 来实现字符串多语言
 * 制作多语言文件方法:
 * 提取需要翻译的字符串,非utf-8编码需要使用--from-code参数来指定
 * $ xgettext --from-code=utf-8 -n --language=PHP -o app/locale/zh_CN/LC_MESSAGES/myapp.po $(find app/ -name '*.php' -o -name '*.html')
 * 得到的 myapp.po 文件为 UTF-8
 * 转换到非utf-8编码下工作,不然使用_("str")输出后会乱码
 * $ msgconv --to-code=gbk myapp.po -o myapp.po
 * 生成.mo文件
 * $ msgfmt -o Jec.mo myapp.po
 * #############################################
 * 注意如果无法正常切换语言，需要修改/var/lib/locales/supported.d/locale文件（UBUNTU下）
 * 添加你需要支持的语言
 * 可能需要运行dpkg-reconfigure locales
 * #############################################
 * mo文件会给php缓存起来，修改后记得重启php进程 (service php-fpm restart)
 */

class Lang
{
    /**
     * @static
     * 初始化多语言模块
     */
    public static function init()
    {
        global $CONFIG;

        $lang = $CONFIG['lang'];
        if(!$lang)
        {
            $lang = array(
                'locale' => 'zh_CN',
                'encoding' => 'utf-8', //语言文件编码
            );
        }
        //动态切换语言版本
        if($CONFIG['openAutoLang'])
        {
            if($auto_lang = self::getAcceptLanguage())
                $lang['locale'] =  $auto_lang;
        }
        //putenv('LC_ALL='.$lang['locale']);
        //putenv("LANGUAGE={$lang['locale']}");
        putenv("LANG={$lang['locale']}");

        setlocale(LC_ALL, $lang['locale'] . ".utf8",
                        $lang['locale'] . ".UTF8",
                        $lang['locale'] . ".utf-8",
                        $lang['locale'] . ".UTF-8",
                        $lang['locale']);

        // 翻译文件必须在路径： locale/zh_CN/LC_MESSAGES/myAppPhp.mo
        bindtextdomain($CONFIG['app_name'], APP_PATH . "/locale");
        bind_textdomain_codeset($CONFIG['app_name'], $lang['encoding']);

        // Choose domain
        textdomain($CONFIG['app_name']);

    }

    public static function getAcceptLanguage()
    {
        $lang = null;
        //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。
        $a_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);

        switch($a_lang)
        {
            case (preg_match("/zh-c/i", $a_lang) ? true : false)://简体中文
                $lang = 'zh_CN';
                break;
            case (preg_match("/zh/i", $a_lang) ? true : false)://繁體中文(台湾、香港、澳门、新加坡)
                $lang = 'zh_TW';
                break;
            case (preg_match("/en/i", $a_lang) ? true : false)://英文
                $lang = 'en_US';
                break;
        }
        return $lang;
    }
}