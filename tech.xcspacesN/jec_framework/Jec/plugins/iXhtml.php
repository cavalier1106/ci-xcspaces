<?php
/**
 * @copyright Jec
 * @package Jec框架
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 * 转换HTML到XHTML
 */

class iXhtml
{
    private $emptyTags = array('area' => 1, 'base' => 1, 'basefont' => 1, 'br' => 1, 'col' => 1, 'frame' => 1, 'hr' => 1, 'img' => 1, 'input' => 1, 'isindex' => 1, 'link' => 1, 'meta' => 1, 'param' => 1, 'embed' => 1,);
    private $blockTags = array('address' => 1, 'applet' => 1, 'blockquote' => 1, 'button' => 1, 'center' => 1, 'dd' => 1, 'dir' => 1, 'div' => 1, 'dl' => 1, 'dt' => 1, 'fieldset' => 1, 'form' => 1, 'frameset' => 1, 'hr' => 1, 'iframe' => 1, 'ins' => 1, 'isindex' => 1, 'li' => 1, 'map' => 1, 'menu' => 1, 'noframes' => 1, 'noscript' => 1, 'object' => 1, 'ol' => 1, 'p' => 1, 'pre' => 1, 'script' => 1, 'table' => 1, 'tbody' => 1, 'td' => 1, 'tfoot' => 1, 'th' => 1, 'thead' => 1, 'tr' => 1, 'ul' => 1,);
    private $inlineTags = array('a' => 1, 'abbr' => 1, 'acronym' => 1, 'applet' => 1, 'b' => 1, 'basefont' => 1, 'bdo' => 1, 'big' => 1, 'br' => 1, 'button' => 1, 'cite' => 1, 'code' => 1, 'del' => 1, 'dfn' => 1, 'em' => 1, 'font' => 1, 'i' => 1, 'iframe' => 1, 'img' => 1, 'input' => 1, 'ins' => 1, 'kbd' => 1, 'label' => 1, 'map' => 1, 'object' => 1, 'q' => 1, 's' => 1, 'samp' => 1, 'script' => 1, 'select' => 1, 'small' => 1, 'span' => 1, 'strike' => 1, 'strong' => 1, 'sub' => 1, 'sup' => 1, 'textarea' => 1, 'tt' => 1, 'u' => 1, 'var' => 1,);
    private $closeSelfTags = array('colgroup' => 1, 'dd' => 1, 'dt' => 1, 'li' => 1, 'options' => 1, 'p' => 1, 'td' => 1, 'tfoot' => 1, 'th' => 1, 'thead' => 1, 'tr' => 1,);
    private $fillAttrsTags = array('checked' => 1, 'compact' => 1, 'declare' => 1, 'defer' => 1, 'disabled' => 1, 'ismap' => 1, 'multiple' => 1, 'nohref' => 1, 'noresize' => 1, 'noshade' => 1, 'nowrap' => 1, 'readonly' => 1, 'selected' => 1,);
    private $specialTags = array('script' => 1, 'style' => 1,);
    private $tagReplac = array();//array('b' => 'strong', 'i' => 'em', 's' => 'del', 'strike' => 'del',);
    private $startTag = '/^<(\w+(?:\:\w+)?)((?:\s+[\w-\:]+(?:\s*=\s*(?:(?:"[^"]*")|(?:\'[^\']*\')|[^>\s]+))?)*)\s*(\/?)>/';
    private $endTag = '/^<\/(\w+(?:\:\w+)?)[^>]*>/';
    private $attr = '/([\w-(?:\:\w+)?]+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:\'((?:\\.|[^\'])*)\')|([^>\s]+)))?/';
    private $index, $chars, $stack = array(), $last = '', $text = "", $results = array();

    private $arrFontsize = array(0 => array('n' => 'xx-small', 's' => '8pt',), 1 => array('n' => 'x-small', 's' => '10pt',), 2 => array('n' => 'small', 's' => '12pt',), 3 => array('n' => 'medium', 's' => '14pt',), 4 => array('n' => 'large', 's' => '18pt',), 5 => array('n' => 'x-large', 's' => '24pt',), 6 => array('n' => 'xx-large', 's' => '36pt',),);

    /**
     * @return iXhtml
     */
    public static function getInstance()
    {
        static $instance = null;
        if (!$instance)
        {
            $instance = new iXhtml();
        }
        return $instance;
    }

    /**
     * @param $html
     * @return string
     */
    public function toXHTML($html)
    {
        $this->last = $html;
        while ($html)
        {
            $this->chars = true;
            if (!$this->stack_last() || !$this->specialTags[$this->stack_last()])
            {
                if (strpos($html, "<!") === 0)
                {
                    preg_match('/^<!(?:--)?(.*?)(?:--)?>/', $html, $match);
                    if ($match)
                    {
                        $html            = substr($html, strlen($match[0]));
                        $this->results[] = "<!--" . $match[1] . "-->";
                        $this->chars     = false;
                    }
                } else
                {
                    if (strpos($html, "</") === 0)
                    {
                        preg_match($this->endTag, $html, $match);
                        if ($match)
                        {
                            $html = substr($html, strlen($match[0]));
                            //match[0].replace(endTag, parseEndTag);
                            preg_match($this->endTag, $match[0], $reg);

                            $this->parseEndTag($reg[0], $reg[1]);
                            $this->chars = false;
                        }
                    } else
                    {
                        if (strpos($html, "<") === 0)
                        {
                            preg_match($this->startTag, $html, $match);

                            if ($match)
                            {
                                $html = substr($html, strlen($match[0]));
                                //match[0].replace(startTag, parseStartTag);
                                preg_match($this->startTag, $match[0], $reg);
                                //all, tagName, attr, end
                                $this->parseStartTag($reg[0], $reg[1], $reg[2], $reg[3]);

                                $this->chars = false;
                            }
                        }
                    }
                }
                if ($this->chars)
                {
                    $index           = search('/<[^<>]+>/', $html);
                    $text            = $index < 0 ? $html : substr($html, 0, $index);
                    $html            = $index < 0 ? "" : substr($html, $index);
                    $text            = str_replace(array("<", ">"), array("&lt;", "&gt;"), $text);
                    $this->results[] = $text;
                }
            } else
            {
                $html = preg_replace_callback('/^([\s\S]*?)<\/(?:style|script)>/i', array($this, 'replace_CSS_JS'), $html);

                $this->parseEndTag("", $this->stack_last());
            }

            if ($html == $this->last)
            {
                $this->parseEndTag();
                return implode('', $this->results);
            }
            $this->last = $html;

        }

        $this->parseEndTag();
        $html = implode('', $this->results);

        $html = preg_replace_callback('/<(font)(\s+[^>]+|)?>(((?!<\1(\s+[^>]+)?>)[\s\S])*?)<\/\1>/i', 'iXhtml::font2style', $html);
        $html = preg_replace_callback('/<(font)(\s+[^>]+|)?>(((?!<\1(\s+[^>]+)?>)[\s\S]|<\1(\s+[^>]+)?>((?!<\1(\s+[^>]+)?>)[\s\S])*?<\/\1>)*?)<\/\1>/i', 'iXhtml::font2style', $html);
        $html = preg_replace_callback('/<(font)(\s+[^>]+|)?>(((?!<\1(\s+[^>]+)?>)[\s\S]|<\1(\s+[^>]+)?>((?!<\1(\s+[^>]+)?>)[\s\S]|<\1(\s+[^>]+)?>((?!<\1(\s+[^>]+)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/i', 'iXhtml::font2style', $html);

        return $html;
    }

    private static function font2style($match)
    {
        $styles  = "";
        $all     = $match[0];
        $tag     = $match[1];
        $attrs   = $match[2];
        $content = $match[3];
        if (preg_match('/ face\s*=\s*"\s*([^"]+)\s*"/i', $attrs, $reg))
        {
            $styles .= "font-family:" . $reg[1] . ";";
        }

        if (preg_match('/ size\s*=\s*"\s*(\d+)\s*"/i', $attrs, $reg))
        {
            $styles .= "font-size:" . self::$arrFontsize[$reg[1] - 1]['n'] . ";";
        }

        if (preg_match('/ color\s*=\s*"\s*([^"]+)\s*"/i', $attrs, $reg))
        {
            $styles .= "color:" . $reg[1] . ";";
        }

        if (preg_match('/ style\s*=\s*"\s*([^"]+)\s*"/i', $attrs, $reg))
        {
            $styles .= $reg[1];
        }
        if ($styles)
        {
            $content = '<span style="' . $styles . '">' . $content . "</span>";
        }

        return $content;
    }

    private function processTag($tagName)
    {
        if ($tagName)
        {
            $tagName = strtolower($tagName);
            $tag     = $this->tagReplac[$tagName];
            if ($tag)
            {
                $tagName = $tag;
            }
        } else
        {
            $tagName = "";
        }
        return $tagName;
    }

    private function parseStartTag($tag, $tagName, $rest, $unary)
    {
        $tagName = $this->processTag($tagName);
        if ($this->blockTags[$tagName])
        {
            while ($this->stack_last() && $this->inlineTags[$this->stack_last()])
                $this->parseEndTag("", $this->stack_last());

        }
        if ($this->closeSelfTags[$tagName] && $this->stack_last() == $tagName)
        {
            $this->parseEndTag("", $tagName);
        }

        $unary = $this->emptyTags[$tagName] || !!$unary;
        if (!$unary)
        {
            $this->stack[] = $tagName;
        }
        $this->results[] = "<" . $tagName;
        preg_match_all($this->attr, $rest, $reg);

        if ($reg)
        {
            foreach ($reg[1] as $key => $name)
            {
                $name  = strtolower($reg[1][$key]);
                $value = $reg[2][$key] ? $reg[2][$key] : ($reg[3][$key] ? $reg[3][$key] : ($reg[4][$key] ? $reg[4][$key] : ($this->fillAttrsTags[$name] ? $name : "")));
                if ($value)
                {
                    $this->results[] = " " . $name . '="'
                        . preg_replace('/(^|[^\\\])"/', '$1"', $value) . '"';
                }
            }
        }

        $this->results[] = ($unary ? " /" : "") . ">";

    }

    private function parseEndTag($tag = '', $tagName = '')
    {
        if (!$tagName)
        {
            $pos = 0;
        } else
        {
            $tagName = $this->processTag($tagName);
            for ($pos = count($this->stack) - 1; $pos >= 0; $pos--)
            {
                if ($this->stack[$pos] == $tagName)
                {
                    break;
                }
            }
        }
        if ($pos >= 0)
        {
            for ($i = count($this->stack) - 1; $i >= $pos; $i--)
            {
                $this->results[] = "</" . $this->stack[$i] . ">";
            }
            $this->stack = array_slice($this->stack, 0, $pos);
        }
    }

    public function replace_CSS_JS($match)
    {
        $this->results[] = $match[1];
        return '';
    }

    private function stack_last()
    {
        return end($this->stack);
        /*$array = $this -> stack;
        if(!$array)return '';
        return array_pop($array);*/
    }

}//end class

