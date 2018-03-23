<?php
class html2ubb
{
    private static $arrcode = array();
    private static $cnum = 0;
    private static $mapSize=array('xx-small'=>1,'8pt'=>1,'x-small'=>2,'10pt'=>2,'small'=>3,'12pt'=>3,'medium'=>4,'14pt'=>4,'large'=>5,'18pt'=>5,'x-large'=>6,'24pt'=>6,'xx-large'=>7,'36pt'=>7);
    private static $regSrc='/\s+src\s*=\s*(["\']?)\s*(.+?)\s*\1(\s|$)/i';
    private static $regWidth='/\s+width\s*=\s*(["\']?)\s*(\d+(?:\.\d+)?%?)\s*\1(\s|$)/i';
    private static $regHeight='/\s+height\s*=\s*(["\']?)\s*(\d+(?:\.\d+)?%?)\s*\1(\s|$)/i';
    private static $regBg='/(?:background|background-color|bgcolor)\s*[:=]\s*(["\']?)\s*((rgb\s*\(\s*\d{1,3}%?,\s*\d{1,3}%?\s*,\s*\d{1,3}%?\s*\))|(#[0-9a-f]{3,6})|([a-z]{1,20}))\s*\1/i';
    
    private static function parseCode($match)
    {//code特殊处理
        self::$cnum++;
        $num = self::$cnum;
        self::$arrcode[$num]=$match[0];
        return "[\tubbcodeplace_".$num."\t]";
    }
    
    private static function parseStyle($match)
    {
        $all = $match[0];
        $tag = $match[1];
        $style = $match[2];
        $str = $match[3];

        if(preg_match('/(?:^|;)\s*font-family\s*:\s*([^;]+)/i', $style, $face))
            $str='[font='.$face[1].']'.$str.'[/font]';
        
        if(preg_match('/(?:^|;)\s*font-size\s*:\s*([^;]+)/i', $style, $size))
        {
            $size=self::$mapSize[strtolower($size[1])];
            if($size)$str='[size='.$size.']'.$str.'[/size]';
        }
        
        if(preg_match('/(?:^|;)\s*color\s*:\s*([^;]+)/i', $style, $color))
            $str='[color='.self::formatColor($color[1]).']'.$str.'[/color]';
        
        if(preg_match('/(?:^|;)\s*(?:background|background-color)\s*:\s*([^;]+)/i', $style, $back))
            $str='[back='.self::formatColor($back[1]).']'.$str.'[/back]';
        
        return $str;
    }
    
    private static function formatColor($c)
    {
        if(preg_match('/\s*rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i', $c, $matchs))
        {
            $c='#';
            for($i=1;$i<=3;$i++)
            $c.= dechex($matchs[$i]);
        }
        $c = preg_replace('/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i', '#$1$1$2$2$3$3', $c);
        return $c;
    }
    
    private static function parseHref($match)
    {
        //all,q,url,text
        $url = $match[2];
        $text = $match[3];
        if(!($url&&$text))return '';
        $tag='url';
        $str = '';
        if(preg_match('/^mailto:/i', $url))
        {
            $tag='email';
            $url=preg_replace('/mailto:(.+?)/i', '$1', $url);
        }
        $str='['.$tag;
        if($url!=$text)$str.='='.$url;
        return $str.']'.$text.'[/'.$tag.']';
    }
    
    private static function parseImg($match)
    {//all,attr
        $attr = $match[1];
        preg_match('/\s+emot\s*=\s*(["\']?)\s*(.+?)\s*\1(\s|$)/i',$attr, $emot);
        if($emot)return '[emot='.$emot[2].'/]';
        preg_match(self::$regSrc, $attr, $url);
        preg_match('/\s+alt\s*=\s*(["\']?)\s*(.*?)\s*\1(\s|$)/i', $attr, $alt);
        preg_match(self::$regWidth, $attr, $w);
        preg_match(self::$regHeight, $attr, $h);
        preg_match('/\s+align\s*=\s*(["\']?)\s*(\w+)\s*\1(\s|$)/i', $attr, $align);
        $str='[img';
        $p='';
        if(!$url)return '';
        $p.=$alt[2];
        if($w||$h)$p.=($w?$w[2]:'').','.($h?$h[2]:'');
        if($align)$p.=','.$align[2];
        if($p)$str.='='.$p;
        $str.=']'.$url[2].'[/img]';
        return $str;
    }
    
    private static function parseFlash($match)
    {//all,attr
        $attr = $match[1];
        preg_match(self::$regSrc, $attr, $url);
        preg_match(self::$regWidth, $attr, $w);
        preg_match(self::$regHeight, $attr, $h);
        $str='[flash';
        if(!$url)return '';
        if($w&&$h)$str.='='.$w[2].','.$h[2];
        $str.=']'.$url[2];
        return $str.'[/flash]';
    }
    
    private static function parseVideo($match)
    {//all,attr
        $attr = $match[1];
        preg_match(self::$regSrc, $attr, $url);
        preg_match(self::$regWidth, $attr, $w);
        preg_match(self::$regHeight, $attr, $h);
        preg_match('/\s+autostart\s*=\s*(["\']?)\s*(.+?)\s*\1(\s|$)/i', $attr, $p);
         $str='[media';
         $auto='0';
        if(!$url)return '';
        if($p)if($p[2]=='true')$auto='1';
        if($w&&$h)$str.='='.$w[2].','.$h[2].','.$auto;
        $str.=']'.$url[2];
        return $str.'[/media]';
    }
    
    private static function parseTable($match)
    {//all,attr
        $attr = $match[1];
        $str='[table';
        if($attr)
        {
            preg_match(self::$regWidth, $attr, $w);
            preg_match(self::$regBg, $attr, $b);
            if($w)
            {
                $str.='='.$w[2];
                if($b)$str.=','.$b[2];
            }
        }
        return $str.']';
    }
    
    private static function parseTr($match)
    {
        $attr = $match[1];
        $str='[tr';
        if($attr)
        {
            preg_match(self::$regBg, $attr, $bg);
            if($bg)$str.='='.$bg[2];
        }
        return $str.']';
    }
    
    private static function parseTd($match)
    {
        $attr = $match[1];
        $str='[td';
        if($attr)
        {
            preg_match('/\s+colspan\s*=\s*(["\']?)\s*(\d+)\s*\1(\s|$)/i', $attr, $col);
            preg_match('/\s+rowspan\s*=\s*(["\']?)\s*(\d+)\s*\1(\s|$)/i', $attr, $row);
            preg_match(self::$regWidth, $attr, $w);
            $col=$col?$col[2]:1;
            $row=$row?$row[2]:1;
            if($col>1||$row>1||$w)$str.='='.$col.','.$row;
            if($w)$str.=','.$w[2];
        }
        return $str.']';
    }
    
    private static function parseUl($match)
    {
        $attr = $match[1];
        if($attr)
            preg_match('/\s+type\s*=\s*(["\']?)\s*(.+?)\s*\1(\s|$)/i', $attr, $t);
        return '[list'.($t?'='.$t[2]:'').']';
    }
    
    private static function parseH($match)
    {
        $n = $match[1];
        return '\r\n\r\n[size='.(7-$n).'][b]';
    }
    
    public static function conv($sUBB)
    {
        self::$arrcode=array();
        self::$cnum=0;

        $sUBB=preg_replace('/\s*\r?\n\s*/', '', $sUBB);
        $sUBB=preg_replace('/<(script|style)(\s+[^>]*?)?>[\s\S]*?<\/\1>/i', '', $sUBB);
        $sUBB=preg_replace('/<\!--[\s\S]*?-->/i', '', $sUBB);
        $sUBB=preg_replace('/<br\s*?\/?>/i', "\r\n", $sUBB);
        $sUBB=preg_replace_callback('/\[code\s*(=\s*([^\]]+?))?\]([\s\S]*?)\[\/code\]/i', "html2ubb::parseCode", $sUBB);
        $sUBB=preg_replace('/<(\/?)(b|u|i|s)(\s+[^>]*?)?>/i', '[$1$2]', $sUBB);
        $sUBB=preg_replace('/<(\/?)strong(\s+[^>]*?)?>/i', '[$1b]', $sUBB);
        $sUBB=preg_replace('/<(\/?)em(\s+[^>]*?)?>/i', '[$1i]', $sUBB);
        $sUBB=preg_replace('/<(\/?)(strike|del)(\s+[^>]*?)?>/i', '[$1s]', $sUBB);
        $sUBB=preg_replace('/<(\/?)(sup|sub)(\s+[^>]*?)?>/i', '[$1$2]', $sUBB);
        
        for($i=0;$i<3;$i++)
            $sUBB=preg_replace_callback('/<(span)(?:\s+[^>]*?)?\s+style\s*=\s*"((?:[^"]*?;)*\s*(?:font-family|font-size|color|background|background-color)\s*:[^"]*)"(?: [^>]+)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/i', "html2ubb::parseStyle", $sUBB);

        for($i=0;$i<3;$i++)
            $sUBB=preg_replace('/<(div|p)(?:\s+[^>]*?)?[\s"\';]\s*(?:text-)?align\s*[=:]\s*(["\']?)\s*(left|center|right)\s*\2[^>]*>(((?!<\1(\s+[^>]*?)?>)[\s\S])+?)<\/\1>/i', '[align=$3]$4[/align]', $sUBB);
        
        for($i=0;$i<3;$i++)
            $sUBB=preg_replace('/<(center)(?:\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S])*?)<\/\1>/i', '[align=center]$2[/align]', $sUBB);

        $sUBB=preg_replace_callback('/<a(?:\s+[^>]*?)?\s+href=(["\'])\s*(.+?)\s*\1[^>]*>\s*([\s\S]*?)\s*<\/a>/i', "html2ubb::parseHref", $sUBB);
        
        $sUBB=preg_replace_callback('/<img(\s+[^>]*?)\/?>/i', "html2ubb::parseImg", $sUBB);
        
        $sUBB=preg_replace('/<blockquote(?:\s+[^>]*?)?>([\s\S]+?)<\/blockquote>/i', '[quote]$1[/quote]', $sUBB);
        
        $sUBB=preg_replace_callback('/<embed((?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-shockwave-flash\s*"|\s+classid\s*=\s*"\s*clsid:d27cdb6e-ae6d-11cf-96b8-4445535400000\s*")[^>]*?)\/>/i', "html2ubb::parseFlash", $sUBB);
        
        $sUBB=preg_replace_callback('/<embed((?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-mplayer2\s*"|\s+classid\s*=\s*"\s*clsid:6bf52a52-394a-11d3-b153-00c04f79faa6\s*")[^>]*?)\/>/i', "html2ubb::parseVideo", $sUBB);
        
        $sUBB=preg_replace_callback('/<table(\s+[^>]*?)?>/i', "html2ubb::parseTable", $sUBB);
        $sUBB=preg_replace_callback('/<tr(\s+[^>]*?)?>/i', "html2ubb::parseTr", $sUBB);
        $sUBB=preg_replace_callback('/<(?:th|td)(\s+[^>]*?)?>/i', "html2ubb::parseTd", $sUBB);
        
        $sUBB=preg_replace('/<\/(table|tr)>/i','[/$1]', $sUBB);
        $sUBB=preg_replace('/<\/(th|td)>/i','[/td]', $sUBB);
    
        $sUBB=preg_replace_callback('/<ul(\s+[^>]*?)?>/i', "html2ubb::parseUl", $sUBB);
        $sUBB=preg_replace('/<ol(\s+[^>]*?)?>/i','[list=1]', $sUBB);
        $sUBB=preg_replace('/<li(\s+[^>]*?)?>/i','[*]', $sUBB);
        $sUBB=preg_replace('/<\/li>/i','', $sUBB);
        $sUBB=preg_replace('/<\/(ul|ol)>/i','[/list]', $sUBB);
        $sUBB=preg_replace('/<h([1-6])(\s+[^>]*?)?>/i', "html2ubb::parseH", $sUBB);
        $sUBB=preg_replace('/<\/h[1-6]>/i',"[/b][/size]\r\n\r\n", $sUBB);
        $sUBB=preg_replace('/<address(\s+[^>]*?)?>/i',"\r\n[i]", $sUBB);
        $sUBB=preg_replace('/<\/address>/i',"[i]\r\n", $sUBB);
        for($i=0;$i<3;$i++)
            $sUBB=preg_replace('/<(p)(?:\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/i',"\r\n\r\n$2\r\n\r\n", $sUBB);
        for($i=0;$i<3;$i++)
            $sUBB=preg_replace('/<(div)(?:\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/i',"\r\n$2\r\n", $sUBB);
    
        $sUBB=preg_replace('/((\s|&nbsp;)*\r?\n){3,}/',"\r\n\r\n", $sUBB);//限制最多2次换行
        $sUBB=preg_replace('/^((\s|&nbsp;)*\r?\n)+/','', $sUBB);//清除开头换行
        $sUBB=preg_replace('/((\s|&nbsp;)*\r?\n)+$/','', $sUBB);//清除结尾换行
    
        for($i=1;$i<=self::$cnum;$i++)
            $sUBB=str_replace("[\tubbcodeplace_".$i."\t]", self::$arrcode[$i], $sUBB);

        $sUBB=preg_replace('/<[^<>]+?>/','', $sUBB);//删除所有HTML标签
        $sUBB=preg_replace('/&lt;/i', '<', $sUBB);
        $sUBB=preg_replace('/&gt;/i', '>', $sUBB);
        $sUBB=preg_replace('/&nbsp;/i', ' ', $sUBB);
        $sUBB=preg_replace('/&amp;/i', '&', $sUBB);
    
        return $sUBB;
    }
}
