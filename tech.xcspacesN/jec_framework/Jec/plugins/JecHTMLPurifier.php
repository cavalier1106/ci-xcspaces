<?php
/**
 * User: jecelyin QQ: 249828165
 * Date: 13-4-8
 * Time: 下午12:48
 * XSS过滤
 */

require dirname(__FILE__) . '/htmlpurifier/HTMLPurifier.auto.php';

class JecHTMLPurifier
{
    /**
     * @var HTMLPurifier
     */
    private static $instance=null;

    public static function filter($string, $allowed_elements=array(), $allowed_attributes=array(), $allowed_protocols=array('http', 'https', 'ftp', 'mailto'))
    {
        if(self::$instance !== null)
            return self::$instance->purify($string);

        if(!$allowed_elements)
        {
            $kses_config = require dirname(__FILE__) . '/kses.cfg.php';
            $allowed_html = $kses_config[0];
            //$allowed_protocols = $kses_config[1];
            $allowed_elements   = array();
            $allowed_attributes = array();
            foreach ($allowed_html as $element => $attributes)
            {
                $allowed_elements[$element] = true;
                foreach ($attributes as $attribute => $x)
                {
                    $allowed_attributes["$element.$attribute"] = true;
                }
            }
        }

        $config             = HTMLPurifier_Config::createDefault();

        $config->set('HTML.AllowedElements', $allowed_elements);
        $config->set('HTML.AllowedAttributes', $allowed_attributes);
        $config->set('Cache.DefinitionImpl', null);
        //让p,span等标签支持id属性
        $config->set('Attr.EnableID', true);
        //允许flash
        $config->set('HTML.SafeObject', true);
        $config->set('HTML.SafeEmbed', true);
        $config->set('Output.FlashCompat', true);
        //end
        if ($allowed_protocols !== null)
        {
            $config->set('URI.AllowedSchemes', $allowed_protocols);
        }
        //处理Definition后不允许再进行$config->set(x,x)
        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('a', 'target', new HTMLPurifier_AttrDef_Enum(
          array('_blank','_self','_target','_top')
        ));

        self::$instance = new HTMLPurifier($config);
        return self::$instance->purify($string);
    }
}