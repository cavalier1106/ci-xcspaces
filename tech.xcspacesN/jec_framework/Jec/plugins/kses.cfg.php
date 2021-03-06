<?php
//允许的HTML标签
$allowed_html = array(
    'address' => array(),
    'a' => array(
        'class' => array (),
        'href' => array (),
        'id' => array (),
        'title' => array (),
        'rel' => array (),
        'rev' => array (),
        'name' => array (),
        'target' => array()),
    'abbr' => array(
        'class' => array (),
        'title' => array ()),
    'acronym' => array(
        'title' => array ()),
    'b' => array(),
    'big' => array(),
    'blockquote' => array(
        'id' => array (),
        'cite' => array (),
        'class' => array(),
        'lang' => array(),
        'xml:lang' => array()),
    'br' => array (
        'class' => array ()),
    'caption' => array(
        'align' => array (),
        'class' => array ()),
    'cite' => array (
        'class' => array(),
        'dir' => array(),
        'lang' => array(),
        'title' => array ()),
    'code' => array (
        'style' => array()),
    'col' => array(
        'align' => array (),
        'span' => array (),
        'dir' => array(),
        'style' => array (),
        'valign' => array (),
        'width' => array ()),
    'center' => array(),
    'del' => array(),
    'dd' => array(),
    'div' => array(
        'align' => array (),
        'class' => array (),
        'dir' => array (),
        'lang' => array(),
        'style' => array (),
        'xml:lang' => array()),
    'dl' => array(),
    'dt' => array(),
    'em' => array(),

    'font' => array(
        'color' => array (),
        'face' => array (),
        'size' => array ()),

    'h1' => array(
        'align' => array (),
        'class' => array ()),
    'h2' => array(
        'align' => array (),
        'class' => array ()),
    'h3' => array(
        'align' => array (),
        'class' => array ()),
    'h4' => array(
        'align' => array (),
        'class' => array ()),
    'h5' => array(
        'align' => array (),
        'class' => array ()),
    'h6' => array(
        'align' => array (),
        'class' => array ()),
    'hr' => array(
        'align' => array (),
        'class' => array (),
        'noshade' => array (),
        'size' => array (),
        'width' => array ()),
    'i' => array(),
    'img' => array(
        'alt' => array (),
        'align' => array (),
        'border' => array (),
        'class' => array (),
        'height' => array (),
        'hspace' => array (),
        'longdesc' => array (),
        'vspace' => array (),
        'src' => array (),
        'style' => array (),
        'width' => array ()),
    'ins' => array(
        'cite' => array ()),
    'kbd' => array(),

    'li' => array (
        'class' => array ()),
    'p' => array(
        'class' => array (),
        'align' => array (),
        'dir' => array(),
        'lang' => array(),
        'style' => array (),
        'id' => array (),
        'xml:lang' => array()),
    'pre' => array(
        'style' => array(),
        'width' => array ()),
    'q' => array(
        'cite' => array ()),
    's' => array(),
    'span' => array (
        'style' => array (),
        'class' => array (),
        'dir' => array (),
        'lang' => array (),
        'title' => array (),
        'xml:lang' => array()),
    'strike' => array(),
    'strong' => array(),
    'sub' => array(),
    'sup' => array(),
    'table' => array(
        'align' => array (),
        'bgcolor' => array (),
        'border' => array (),
        'cellpadding' => array (),
        'cellspacing' => array (),
        'class' => array (),
        'dir' => array(),
        'id' => array(),
        'rules' => array (),
        'style' => array (),
        'summary' => array (),
        'width' => array ()),
    'tbody' => array(
        'align' => array (),
        'valign' => array ()),
    'td' => array(
        'abbr' => array (),
        'align' => array (),
        'bgcolor' => array (),
        'class' => array (),
        'colspan' => array (),
        'dir' => array(),
        'height' => array (),
        'nowrap' => array (),
        'rowspan' => array (),
        'scope' => array (),
        'style' => array (),
        'valign' => array (),
        'width' => array ()),

    'tfoot' => array(
        'align' => array (),
        'class' => array (),
        'valign' => array ()),
    'th' => array(
        'abbr' => array (),
        'align' => array (),
        'bgcolor' => array (),
        'class' => array (),
        'colspan' => array (),
        'height' => array (),
        'nowrap' => array (),
        'rowspan' => array (),
        'scope' => array (),
        'valign' => array (),
        'width' => array ()),
    'thead' => array(
        'align' => array (),

        'class' => array (),
        'valign' => array ()),
    'tr' => array(
        'align' => array (),
        'bgcolor' => array (),

        'class' => array (),
        'style' => array (),
        'valign' => array ()),
    'tt' => array(),
    'u' => array(),
    'ul' => array (
        'class' => array (),
        'style' => array (), 
        'type' => array ()),
    'ol' => array (
        'class' => array (),
        'start' => array (),
        'style' => array (), 
        'type' => array ()),
    'object' => array(
        'classid' => array(),
        'codebase' => array(),
        'height' => array (),
        'width' => array (),
    ),
    'param'=>array(
        'name' => array(),
        'value' => array(),
    ),

    'embed' => array (
        'height' => array (),
        'type' => array (),
        'width' => array (), 
        'src' => array (),
        ),
    'var' => array ());
//允许的协议，像css“color: #fff”中的color也当作协议
$allowed_protocols = array('http', 'https', 'ftp', 'news', 'nntp','data','mailto');
return array($allowed_html , $allowed_protocols);
