<?php

class HTMLPurifier_AttrTransform_SafeEmbed extends HTMLPurifier_AttrTransform
{
    public $name = "SafeEmbed";

    public function transform($attr, $config, $context) {
        /*$attr['allowscriptaccess'] = 'never';
        $attr['allownetworking'] = 'internal';*/ //jecelyin+: 会导致flash里按钮无法链接
        $attr['type'] = 'application/x-shockwave-flash';
        return $attr;
    }
}

// vim: et sw=4 sts=4
