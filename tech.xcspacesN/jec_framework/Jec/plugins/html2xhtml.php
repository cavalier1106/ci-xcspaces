<?php

function close_tag($tag, $sample_html) {
  return preg_replace("!(<{$tag}(\s[^>]*[^/>\s])?)\s*>!si","\\1 />",$sample_html);
}

function make_attr_value($attr, $html) {
  return preg_replace("#(<[^>]*\s){$attr}(\s|>|/>)#si","\\1{$attr}=\"{$attr}\"\\2",$html);
}


function mk_open_tag_regexp($tag) { return "<\s*{$tag}(\s+[^>]*)?>"; }
function mk_close_tag_regexp($tag) { return "<\s*/\s*{$tag}\s*>"; }

function process_html($html) {
  $open  = mk_open_tag_regexp("html");
  $close = mk_close_tag_regexp("html");

  if (!preg_match("#{$open}#is",$html)) {
    $html = "<html>".$html;
  }
  if (!preg_match("#{$close}#is",$html)) {
    $html = $html."</html>";
  }

  $html = preg_replace("#.*({$open})#is","\\1",$html);

// Cut off all data before and after 'html' tag; unless we'll do it,
// the XML parser will die violently
  $html = preg_replace("#^.*<html#is","<html",$html);
  $html = preg_replace('#</html\s*>.*$#is',"</html>",$html);

  return $html;
}

function process_head($html) {
  $open  = mk_open_tag_regexp("head");
  $close = mk_close_tag_regexp("head");
  $ohtml = mk_open_tag_regexp("html");
  $chtml = mk_close_tag_regexp("html");
  $obody = mk_open_tag_regexp("body");

  if (!preg_match("#{$open}#is",$html)) {
    $html = preg_replace("#({$ohtml})(.*)({$obody})#is","\\1<head>\\3</head>\\4",$html);
  } elseif (!preg_match("#{$close}#is",$html)) {
    if (preg_match("#{$obody}#is",$html)) {
      $html = preg_replace("#({$obody})#is","</head>\\1",$html);
    } else {
      $html = preg_replace("#({$chtml})#is","</head>\\1",$html);
    }
  }
  return $html;
}

function process_body($html) {
  $open  = mk_open_tag_regexp("body");
  $close = mk_close_tag_regexp("body");
  $ohtml = mk_open_tag_regexp("html");
  $chtml = mk_close_tag_regexp("html");
  $chead = mk_close_tag_regexp("head");

  if (!preg_match("#{$open}#is",$html)) {
    if (preg_match("#{$chead}#is",$html)) {
      $html = preg_replace("#({$chead})#is","\\1<body>",$html);
    } else {
      $html = preg_replace("#({$ohtml})#is","\\1<body>",$html);
    }
  }
  if (!preg_match("#{$close}#is",$html)) {
    $html = preg_replace("#({$chtml})#is","</body>\\1",$html);
  }

  // Now check is there any data between </head> and <body>.
  $html = preg_replace("#({$chead})(.+)({$open})#is","\\1\\3\\2",$html);
  // Check if there's any data between </body> and </html>
  $html = preg_replace("#({$close})(.+)({$chtml})#is","\\2\\1\\3",$html);

  return $html;
}

// Hmmm. May be we'll just write SAX parser on PHP? ;-)
function fix_tags($html) {
  $result = "";
  $tag_stack = array();

  // these corrections can simplify the regexp used to parse tags
  // remove whitespaces before '/' and between '/' and '>' in autoclosing tags
  $html = preg_replace('#\s*/\s*>#is'," />",$html);
  // remove whitespaces between '<', '/' and first tag letter in closing tags
  $html = preg_replace('#<\s*/\s*#is',"</",$html);
  // remove whitespaces between '<' and first tag letter
  $html = preg_replace('#<\s+#is',"<",$html);

  while (preg_match('#(.*?)(<([a-z\d]+)[^>]*/>|<([a-z\d]+)[^>]*(?<!/)>|</([a-z\d]+)[^>]*>)#is',$html,$matches)) {
    $result .= $matches[1];
    $html = substr($html, strlen($matches[0]));

    // Closing tag
    if (isset($matches[5])) {
      $tag = $matches[5];

      if (isset($tag_stack[0]) && $tag == $tag_stack[0]) {
        // Matched the last opening tag (normal state)
        // Just pop opening tag from the stack
        array_shift($tag_stack);
        $result .= $matches[2];
      } elseif (array_search($tag, $tag_stack)) {
        // We'll never should close 'table' tag such way, so let's check if any 'tables' found on the stack
        
        $no_critical_tags = !array_search('table',$tag_stack);
        if (!$no_critical_tags) {
          $no_critical_tags = (array_search('table',$tag_stack) >= array_search($tag, $tag_stack));
        }

        if ($no_critical_tags) {
          // Corresponding opening tag exist on the stack (somewhere deep)
          // Note that we can forget about 0 value returned by array_search, becaus it is handled by previous 'if'

          // Insert a set of closing tags for all non-matching tags
          $i = 0;
          while ($tag_stack[$i] != $tag) {
            $result .= "</{$tag_stack[$i]}> ";
            $i++;
          }

          // close current tag
          $result .= "</{$tag_stack[$i]}> ";
          // remove it from the stack
          array_splice($tag_stack, $i, 1);
          // if this tag is not "critical", reopen "run-off" tags
          $no_reopen_tags = array("tr","td","table","marquee","body","html");
          if (array_search($tag, $no_reopen_tags) === false) {
            while ($i > 0) {
              $i--;
              $result .= "<{$tag_stack[$i]}> ";
            }
          } else {
            array_splice($tag_stack, 0, $i);
          }
        }
      } else {
        // No such tag found on the stack, just remove it (do nothing in out case, as we have to explicitly
        // add things to result
        
      }
    } elseif (isset($matches[4])) {
      // Opening tag
      $tag = $matches[4];
      array_unshift($tag_stack, $tag);
      $result .= $matches[2];
    } else {
      // Autoclosing tag; do nothing specific
      $result .= $matches[2];
    }
  }

  if(!$tag_stack)return empty($result) ? $html : $result;
  // Close all tags left
  while (count($tag_stack) > 0) {
    $tag = array_shift($tag_stack);
    $result .= "</".$tag.">";
  }

  return $result;
}

function quote_attrs($html) {
  while (preg_match('!(<[^>]*)\s([^=>]+)=([^\'"\r\n >]+)([\r\n >])!si',$html, $matches)) {
    $html = preg_replace('#(<[^>]*)\s([^=>]+)=([^\'"\r\n >]+)([\r\n >])#si',"\\1 \\2='\\3'\\4",$html);
  }
  return $html;
}

function fix_attrs_spaces(&$html) {
  while (preg_match('#(<[^>]*)\s([^\s=>]+)="([^"]*?)"([^\s])#si', $html)) {
    $html = preg_replace('#(<[^>]*)\s([^\s=>]+)="([^"]*?)"([^\s])#si',"\\1 \\2=\"\\3\" \\4",$html);
  }

  while (preg_match('#(<[^>]*)\s([^\s=>]+)=\'([^\']*?)\'([^\s])#si', $html)) {
    $html = preg_replace("#(<[^>]*)\s([^\s=>]+)='([^']*?)'([^\s])#si","\\1 \\2='\\3' \\4",$html);
  }
}

function fix_attrs_tag($tag) {
  if (preg_match("#(<)(.*?)(/\s*>)#is",$tag, $matches)) {
    $prefix  = $matches[1];
    $suffix  = $matches[3];
    $content = $matches[2];
  } elseif (preg_match("#(<)(.*?)(>)#is",$tag, $matches)) {
    $prefix  = $matches[1];
    $suffix  = $matches[3];
    $content = $matches[2];
  } else {
    return;
  }

  if (preg_match("#^\s*(\w+)\s*(.*)\s*/\s*\$#is", $content, $matches)) {
    $tagname   = $matches[1];
    $raw_attrs = isset($matches[2]) ? $matches[2] : "";
  } elseif (preg_match("#^\s*(\w+)\s*(.*)\$#is", $content, $matches)) {
    $tagname   = $matches[1];
    $raw_attrs = isset($matches[2]) ? $matches[2] : "";
  } else {
    // A strange tag occurred; just remove everything
    $tagname   = "";
    $raw_attrs = "";
  }

  $attrs = array();
  while (!empty($raw_attrs)) {
    if (preg_match("#^\s*(\w+?)\s*=\s*\"(.*?)\"(.*)$#is",$raw_attrs,$matches)) {
      $attr  = strtolower($matches[1]);
      $value = $matches[2];

      if (!isset($attrs[$attr])) {
        $attrs[$attr] = $value;
      }

      $raw_attrs = $matches[3];
    } elseif (preg_match("#^\s*(\w+?)\s*=\s*'(.*?)'(.*)$#is",$raw_attrs,$matches)) {
      $attr  = strtolower($matches[1]);
      $value = $matches[2];

      if (!isset($attrs[$attr])) {
        $attrs[$attr] = $value;
      }

      $raw_attrs = $matches[3];
    } elseif (preg_match("#^\s*(\w+?)=(\w+)(.*)$#is",$raw_attrs,$matches)) {
      $attr  = strtolower($matches[1]);
      $value = $matches[2];

      if (!isset($attrs[$attr])) {
        $attrs[$attr] = $value;
      }

      $raw_attrs = $matches[3];
    } elseif (preg_match("#^\s*\S+\s+(.*)$#is",$raw_attrs,$matches)) {
      // Just a junk at the beginning; skip till the first space
      $raw_attrs = $matches[1];
    } else {
      $raw_attrs = "";
    }
  }

  $str = "";
  foreach ($attrs as $key => $value) {
    // In theory, if the garbage have been found inside the attrs section, we could get
    // and invalid attribute name here; just ignore them in this case
    if (HTML2PS_XMLUtils::valid_attribute_name($key)) {
      if (strpos($value,'"') !== false) {
        $str .= " ".$key."='".$value."'";
      } else {
        $str .= " ".$key."=\"".$value."\"";
      }
    }
  }

  return $prefix.$tagname.$str.' '.$suffix;
}

function fix_attrs($html) {
  $result = "";

  while (preg_match("#^(.*?)(<[^/].*?>)#is",$html,$matches)) {
    $result .= $matches[1].fix_attrs_tag($matches[2]);
    $html = substr($html, strlen($matches[0]));
  }

  return $result.$html;
}

function fix_closing_tags($html) {
  return preg_replace("#</\s*(\w+).*?>#","</\\1>",$html);
}

function process_pagebreak_commands(&$html) {
  $html = preg_replace("#<\?page-break>|<!--NewPage-->#","<pagebreak/>",$html);
}

function process_script($sample_html) {
  return preg_replace("#<script.*?</script>#is","",$sample_html);
}

function autoclose_tag(&$sample_html, $offset, $tags, $nested, $close) {
  $tags = mk_open_tag_regexp($tags);

  while (preg_match("#^(.*?)({$tags})#is", substr($sample_html, $offset),$matches)) {
    // convert tag name found to lower case
    $tag = strtolower($matches[3]);
    // calculate position of the tag found
    $tag_start = $offset + strlen($matches[1]);
    $tag_end   = $tag_start + strlen($matches[2]);

    if ($tag == $close) { return $tag_end; }

    // REQ: PHP 4.0.5
    if (isset($nested[$tag])) {
      $offset = $nested[$tag]($sample_html, $tag_end);
    } else {
      $to_be_inserted = "<".$close.">";

      $sample_html = substr_replace($sample_html, $to_be_inserted, $tag_start ,0);
      return $tag_start + strlen($to_be_inserted);
    }
  }

  return $offset;
}

// removes from current html string a piece from the current $offset to
// the beginning of next $tag; $tag should contain a '|'-separated list
// of opening or closing tags. This function is useful for cleaning up
// messy code containing trash between TD, TR and TABLE tags.
function skip_to(&$html, $offset, $tag) {
  $prefix = substr($html,0,$offset);
  $suffix = substr($html,$offset);

  if (preg_match("#^(.*?)<\s*({$tag})#is", $suffix, $matches)) {
    $suffix = substr($suffix, strlen($matches[1]));
  }

  $html = $prefix . $suffix;
}

function autoclose_tag_cleanup(&$sample_html, $offset, $tags_raw, $nested, $close) {
  $tags = mk_open_tag_regexp($tags_raw);
  skip_to($sample_html, $offset, $tags_raw);

  while (preg_match("#^(.*?)({$tags})#is", substr($sample_html, $offset),$matches)) {
    // convert tag name found to lower case
    $tag = strtolower($matches[3]);
    // calculate position of the tag found
    $tag_start = $offset + strlen($matches[1]);
    $tag_end   = $tag_start + strlen($matches[2]);

    if ($tag == $close) { return $tag_end; }

    // REQ: PHP 4.0.5
    if (isset($nested[$tag])) {
      $offset = $nested[$tag]($sample_html, $tag_end);
    } else {
      $to_be_inserted = "<".$close.">";

      $sample_html = substr_replace($sample_html, $to_be_inserted, $tag_start ,0);
      return $tag_start + strlen($to_be_inserted);
    }

    skip_to($sample_html, $offset, $tags_raw);
  }

  return $offset;
}

function remove_comments(&$html) {
  $html = preg_replace("#<!--.*?-->#is","",$html);
  $html = preg_replace("#<!.*?>#is","",$html);
}

function process_dd(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|dl|/dl|/dd)", array("dl" => "process_dl"), "/dd");
}

function process_dt(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|dl|/dl|/dd)", array("dl" => "process_dl"), "/dt");
}

function process_dl(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dt|dd|/dl)",
                       array("dt" => "process_dt",
                             "dd" => "process_dd"),
                       "/dl");
}

function process_deflists(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(dl)",
                       array("dl" => "process_dl"),
                       "");
}

function process_li(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(ul|ol|li|/li|/ul|/ol)",
                       array("ul" => "process_ul",
                             "ol" => "process_ol"),
                       "/li");
}

function process_ol(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(li|/ol)",
                       array("li" => "process_li"),
                       "/ol");
}

function process_ul(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(li|/ul)",
                       array("li" => "process_li"),
                       "/ul");
}

function process_lists(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(ul|ol)",
                       array("ul" => "process_ul",
                             "ol" => "process_ol"),
                       "");
}

function process_p($sample_html) {
  $open_regexp = implode("|",
    array(
      "p","dl","div","noscript","blockquote","form","hr","table","fieldset","address",
      "ul","ol","li",
      "h1","h2","h3","h4","h5","h6",
      "pre", "frameset", "noframes"
    )
  );
  $close_regexp = implode("|",
    array(
      "dl","div","noscript","blockquote","form","hr","table","fieldset","address",
      "ul","ol","li",
      "h1","h2","h3","h4","h5","h6",
      "pre", "frameset", "noframes", "body"
    )
  );
  $open = mk_open_tag_regexp("(".$open_regexp.")");
  $close = mk_close_tag_regexp("(".$close_regexp.")");

  $offset = 0;
  while (preg_match("#^(.*?)(<\s*p(\s+[^>]*?)?>)(.*?)($open|$close)#is",substr($sample_html, $offset), $matches)) {
    if (!preg_match("#<\s*/\s*p\s*>#is",$matches[3])) {
      $cutpos = $offset + strlen($matches[1]) + strlen($matches[2]) + strlen($matches[4]);
      $sample_html = substr_replace($sample_html, "</p>", $cutpos, 0);
      $offset = $cutpos+4;
    } else {
      $offset += strlen($matches[1])+1;
    }
  }

  return $sample_html;
}

function process_option(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select|/option)",
                       array(),
                       "/option");
}

function process_select(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select)",
                       array("option" => "process_option"),
                       "/select");
}

function process_selects(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(select)",
                       array("select" => "process_select"),
                       "");
}

function process_style(&$html) {
  // Remove HTML comment bounds inside the <style>...</style>
  $html = preg_replace("#(<style[^>]*>)\s*<!--#is","\\1",$html);
  $html = preg_replace("#-->\s*(</style>)#is","\\1",$html);

  // Remove CSS comments
  while (preg_match("#(<style[^>]*>.*)/\*.*?\*/.*(</style>)#is",$html)) {
    $html = preg_replace("#(<style[^>]*>.*)/\*.*\*/(.*</style>)#is","\\1\\2",$html);
  }
}

function process_cell(&$sample_html, $offset) {
  $r = autoclose_tag($sample_html, $offset,
                       "(table|td|th|tr|thead|tbody|tfoot|/td|/th|/table|/thead|/tbody|/tfoot|/tr)",
                       array("table" => "process_table"),
                       "/td");
  return $r;
}

function process_header_cell(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset,
                       "(table|td|th|tr|thead|tbody|tfoot|/td|/th|/table|/thead|/tbody|/tfoot|/tr)",
                       array("table" => "process_table"),
                       "/th");
}

function process_cell_without_row(&$html, $offset) {
  // Insert missing <tr> tag and fall to the 'process_row'

  // get the LAST tag before offset point; it should be the TD tag outside the row
  preg_match("#<[^>]+>$#",substr($html,0,$offset),$matches);

  // Now 'matches' contains the bad TD tag (opening)

  // Insert the TR tag before the TD found
  $html = substr_replace($html, "<tr>".$matches[0], $offset - strlen($matches[0]), strlen($matches[0]));

  // Restart row processing from the beginning of inserted TR (not inclusing the TR tag itself!, as it will cause the closing
  // tag to be inserted automatically)
  //
  $r = process_row($html, $offset - strlen($matches[0]) + strlen("<tr>"));

  return $r;
}

function process_row(&$sample_html, $offset) {
  return autoclose_tag_cleanup($sample_html, $offset,
                               "(td|th|thead|tbody|tfoot|tr|/table|/thead|/tbody|/tfoot|/tr)",
                               array("td" => "process_cell",
                                     "th" => "process_header_cell"),
                               "/tr");
}


function process_rowgroup($group, &$sample_html, $offset) {
  return autoclose_tag_cleanup($sample_html, $offset,
                               "(thead|tbody|tfoot|tr|/table|/{$group})",
                               array("tr" => "process_row"),
                               "/{$group}");
}

function process_thead(&$html, $offset) { return process_rowgroup('thead', $html, $offset); }
function process_tbody(&$html, $offset) { return process_rowgroup('tbody', $html, $offset); }
function process_tfoot(&$html, $offset) { return process_rowgroup('tfoot', $html, $offset); }

function process_col(&$html, $offset) {
  // As COL is self-closing tag, we just continue processing
  return $offset;
}

function process_col_without_colgroup(&$html, $offset) {
  // Insert missing <colgroup> tag and fall to the 'process_colgroup'

  // get the LAST tag before offset point; it should be the COL tag outside the COLGROUP
  preg_match("#<[^>]+>$#",substr($html,0,$offset),$matches);

  // Now 'matches' contains this COL tag (self-closing)

  // Insert the COLGROUP tag before the COL found
  $sample_html = substr_replace($html, "<colgroup>".$matches[0], $offset - strlen($matches[0]), strlen($matches[0]));

  // Restart colgroup processing from the beginning of inserted COLGROUP
  return process_colgroup($html, $offset - strlen($matches[0]));
}

function process_colgroup(&$html, $offset) {
  return autoclose_tag_cleanup($html, $offset,
                               "(col|colgroup|thead|tbody|tfoot|tr|td|th|/colgroup)",
                               array("col"      => "process_col"),
                               "/colgroup");
}

function process_table(&$html, $offset) {
  return autoclose_tag_cleanup($html, $offset,
                               "(col|colgroup|thead|tbody|tfoot|tr|td|th|/table)",
                               array("col"      => "process_col_without_colgroup",
                                     "colgroup" => "process_colgroup",
                                     "thead"    => "process_thead",
                                     "tbody"    => "process_tbody",
                                     "tfoot"    => "process_tfoot",
                                     "tr"       => "process_row",
                                     "td"       => "process_cell_without_row",
                                     "th"       => "process_cell_without_row"),
                               "/table");
}

function process_tables(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset,
                       "(table)",
                       array("table" => "process_table"),
                       "");
}

function process_character_references(&$html) {
  // Process symbolic character references
  global $g_html_entities;
  foreach ($g_html_entities as $entity => $code) {
    $html = str_replace("&{$entity}","&#{$code}",$html);

    // Some ill-brained webmasters write HTML symbolic references without
    // terminating semicolor (especially at www.whitehouse.gov. The following
    // replacemenet is required to fix these damaged inteties, converting them
    // to the numerical character reference.
    //
    // We use [\s<] as entity name terminator to avoid breaking up longer entity
    // names by filtering in only space or HTML-tag terminated ones.
    //
    $html = preg_replace("/&{$entity}([\s<])/","&#{$code}\\1",$html);
  }

  // Process hecadecimal character references
  while (preg_match("/&#x([[:xdigit:]]{2,4});/i", $html, $matches)) {
    // We cannot use plain str_replace, because 'x' symbol can be in both cases;
    // str_ireplace have appeared in PHP 5 only, so we cannot use it due the
    // compatibility problems

    $html = preg_replace("/&#x".$matches[1].";/i","&#".hexdec($matches[1]).";",$html);
  }
}

function escape_amp($html) {
  // Escape all ampersants not followed by a # sharp sign
  // Note that symbolic references were replaced by numeric before this!
  $html = preg_replace("/&(?!#)/si","&#38;\\1",$html);

  // Complete all numeric character references unterminated with ';'
  $html = preg_replace("/&#(\d+)(?![\d;])/si","&#\\1;",$html);

  // Escape all ampersants followed by # sharp and NON-DIGIT symbol
  // They we're not covered by above conversions and are not a
  // symbol reference.
  // Also, don't forget that we've used &amp;! They should not be converted too...
  //
  $html = preg_replace("/&(?!#\d)/si","&#38;\\1",$html);

  return $html;
}

function escape_lt($html) {
  // Why this loop is needed here?
  // The cause is that, for example, <<<a> sequence will not be replaced by
  // &lt;&lt<a>, as it should be. The regular expression matches TWO symbols
  // << (actually, first < symbold, and one following it, so, the second <
  // will not be matched when script attempt to find and replace next occurrence using 'g' regexp
  // modifier. So, we will need to check for such situations agint and, possibly, restart the
  // search and replace process.
  //
  while (preg_match("#<(\s*[^!/a-zA-Z])#",$html)) {
    $html = preg_replace("#<(\s*[^!/a-zA-Z])#si","&#60;\\1",$html);
  }

  while (preg_match("#(<[^>]*?)<#si",$html)) {
    $html = preg_replace("#(<[^>]*?)<#si","\\1&#60;",$html);
  }

  return $html;
}

function escape_gt($html) {
  $html = preg_replace("#([^\s\da-zA-Z'\"/=-])\s*>#si","\\1&#62;",$html);

  while (preg_match("#(>[^<]*?)>#si",$html)) {
    $html = preg_replace("#(>[^<]*?)>#si","\\1&#62;",$html);
  }

  return $html;
}



function xhtml2xhtml($html) {
  process_pagebreak_commands($html);

  // Do HTML -> XML (XHTML) conversion
  // Convert HTML character references to their Unicode analogues
  process_character_references($html);

  // Remove HTML and CSS comments inside STYLE tags
  process_style($html);

  remove_comments($html);

  // Convert all tags to lower case
  $html = lowercase_tags($html);
  $html = lowercase_closing_tags($html);

  // Remove SCRIPT tags
  $html = process_script($html);

  return $html;
}

function html2xhtml($html) {
  process_pagebreak_commands($html);

  // Remove SCRIPT tags from the page being processed, as script content may
  // mess the firther html-parsing utilities
  //$html = process_script($html);

  // Convert HTML character references to their Unicode analogues
 // process_character_references($html);

  process_style($html);

  //remove_comments($html);

  fix_attrs_spaces($html);
  $html = quote_attrs($html);

  $html = lowercase_tags($html);
  $html = lowercase_closing_tags($html);

  $html = fix_closing_tags($html);

  $html = close_tag("area",$html);
  $html = close_tag("base",$html);
  $html = close_tag("basefont",$html);
  $html = close_tag("br",$html);
  $html = close_tag("col",$html);
  $html = close_tag("embed",$html);
  $html = close_tag("frame",$html);
  $html = close_tag("hr",$html);
  $html = close_tag("img",$html);
  $html = close_tag("input",$html);
  $html = close_tag("isindex",$html);
  $html = close_tag("link",$html);
  $html = close_tag("meta",$html);
  $html = close_tag("param",$html);

  $html = make_attr_value("checked",$html);
  $html = make_attr_value("compact",$html);
  $html = make_attr_value("declare",$html);
  $html = make_attr_value("defer",$html);
  $html = make_attr_value("disabled",$html);
  $html = make_attr_value("ismap",$html);
  $html = make_attr_value("multiple",$html);
  $html = make_attr_value("nohref",$html);
  $html = make_attr_value("noresize",$html);
  $html = make_attr_value("noshade",$html);
  $html = make_attr_value("nowrap",$html);
  $html = make_attr_value("readonly",$html);
  $html = make_attr_value("selected",$html);

  //$html = process_html($html);
  //$html = process_body($html);
  //$html = process_head($html);
  $html = process_p($html);

  //$html = escape_amp($html);
  //$html = escape_lt($html);
  //$html = escape_gt($html);

  process_tables($html,0);

  process_lists($html,0);
  process_deflists($html,0);
  process_selects($html,0);

  $html = fix_tags($html);
  $html = fix_attrs($html);

  return $html;
}

function lowercase_tags($html) {
  $result = "";

  while (preg_match("#^(.*?)(</?)([a-zA-z0-9]+)([\s>])#is",$html,$matches)) {
    // Drop extracted part
    $html = substr($html,strlen($matches[0]));
    // Move extracted part to the result
    $result .= $matches[1].$matches[2].strtolower($matches[3]).$matches[4];
  }

  return $result.$html;
}

function lowercase_closing_tags($html) {
  $result = "";

  while (preg_match("#^(.*?)(<)([a-zA-z0-9]+)(\s*/\s*>)#is",$html,$matches)) {
    // Drop extracted part
    $html = substr($html,strlen($matches[0]));
    // Move extracted part to the result
    $result .= $matches[1].$matches[2].strtolower($matches[3]).$matches[4];
  }

  return $result.$html;
}

class HTML2PS_XMLUtils {
  function valid_attribute_name($name) {
    // Note that, technically, it is not correct, as XML standard treats as letters
    // characters other than a-z too.. Nevertheless, this simple variant
    // will do for XHTML/HTML

    return preg_match("/[a-z_:][a-z0-9._:.]*/i",$name);
  }
}

  $g_html_entities = array(
                           "nbsp" => "160", /* no-break space = non-breaking space,U+00A0 ISOnum  */
                           "iexcl" => "161", /* inverted exclamation mark, U+00A1 ISOnum  */
                           "cent" => "162", /* cent sign, U+00A2 ISOnum  */
                           "pound" => "163", /* pound sign, U+00A3 ISOnum  */
                           "curren" => "164", /* currency sign, U+00A4 ISOnum  */
                           "yen" => "165", /* yen sign = yuan sign, U+00A5 ISOnum  */
                           "brvbar" => "166", /* broken bar = broken vertical bar,U+00A6 ISOnum  */
                           "sect" => "167", /* section sign, U+00A7 ISOnum  */
                           "uml" => "168", /* diaeresis = spacing diaeresis,U+00A8 ISOdia  */
                           "copy" => "169", /* copyright sign, U+00A9 ISOnum  */
                           "ordf" => "170", /* feminine ordinal indicator, U+00AA ISOnum  */
                           "laquo" => "171", /* left-pointing double angle quotation mark = left pointing guillemet, U+00AB ISOnum */
                           "not" => "172", /* not sign, U+00AC ISOnum  */
                           "shy" => "173", /* soft hyphen = discretionary hyphen,U+00AD ISOnum  */
                           "reg" => "174", /* registered sign = registered trade mark sign,U+00AE ISOnum  */
                           "macr" => "175", /* macron = spacing macron = overline = APL overbar, U+00AF ISOdia */
                           "deg" => "176", /* degree sign, U+00B0 ISOnum  */
                           "plusmn" => "177", /* plus-minus sign = plus-or-minus sign,U+00B1 ISOnum  */
                           "sup2" => "178", /* superscript two = superscript digit two = squared, U+00B2 ISOnum */
                           "sup3" => "179", /* superscript three = superscript digit three = cubed, U+00B3 ISOnum */
                           "acute" => "180", /* acute accent = spacing acute,U+00B4 ISOdia  */
                           "micro" => "181", /* micro sign, U+00B5 ISOnum  */
                           "para" => "182", /* pilcrow sign = paragraph sign,U+00B6 ISOnum  */
                           "middot" => "183", /* middle dot = Georgian comma = Greek middle dot, U+00B7 ISOnum */
                           "cedil" => "184", /* cedilla = spacing cedilla, U+00B8 ISOdia  */
                           "sup1" => "185", /* superscript one = superscript digit one,U+00B9 ISOnum  */
                           "ordm" => "186", /* masculine ordinal indicator,U+00BA ISOnum  */
                           "raquo" => "187", /* right-pointing double angle quotation mark = right pointing guillemet, U+00BB ISOnum */
                           "frac14" => "188", /* vulgar fraction one quarter = fraction one quarter, U+00BC ISOnum */
                           "frac12" => "189", /* vulgar fraction one half = fraction one half, U+00BD ISOnum */
                           "frac34" => "190", /* vulgar fraction three quarters = fraction three quarters, U+00BE ISOnum */
                           "iquest" => "191", /* inverted question mark = turned question mark, U+00BF ISOnum */
                           "Agrave" => "192", /* latin capital letter A with grave = latin capital letter A grave, U+00C0 ISOlat1 */
                           "Aacute" => "193", /* latin capital letter A with acute,U+00C1 ISOlat1  */
                           "Acirc" => "194", /* latin capital letter A with circumflex,U+00C2 ISOlat1  */
                           "Atilde" => "195", /* latin capital letter A with tilde,U+00C3 ISOlat1  */
                           "Auml" => "196", /* latin capital letter A with diaeresis,U+00C4 ISOlat1  */
                           "Aring" => "197", /* latin capital letter A with ring above = latin capital letter A ring, U+00C5 ISOlat1 */
                           "AElig" => "198", /* latin capital letter AE = latin capital ligature AE, U+00C6 ISOlat1 */
                           "Ccedil" => "199", /* latin capital letter C with cedilla,U+00C7 ISOlat1  */
                           "Egrave" => "200", /* latin capital letter E with grave,U+00C8 ISOlat1  */
                           "Eacute" => "201", /* latin capital letter E with acute,U+00C9 ISOlat1  */
                           "Ecirc" => "202", /* latin capital letter E with circumflex,U+00CA ISOlat1  */
                           "Euml" => "203", /* latin capital letter E with diaeresis,U+00CB ISOlat1  */
                           "Igrave" => "204", /* latin capital letter I with grave,U+00CC ISOlat1  */
                           "Iacute" => "205", /* latin capital letter I with acute,U+00CD ISOlat1  */
                           "Icirc" => "206", /* latin capital letter I with circumflex,U+00CE ISOlat1  */
                           "Iuml" => "207", /* latin capital letter I with diaeresis,U+00CF ISOlat1  */
                           "ETH" => "208", /* latin capital letter ETH, U+00D0 ISOlat1  */
                           "Ntilde" => "209", /* latin capital letter N with tilde,U+00D1 ISOlat1  */
                           "Ograve" => "210", /* latin capital letter O with grave,U+00D2 ISOlat1  */
                           "Oacute" => "211", /* latin capital letter O with acute,U+00D3 ISOlat1  */
                           "Ocirc" => "212", /* latin capital letter O with circumflex,U+00D4 ISOlat1  */
                           "Otilde" => "213", /* latin capital letter O with tilde,U+00D5 ISOlat1  */
                           "Ouml" => "214", /* latin capital letter O with diaeresis,U+00D6 ISOlat1  */
                           "times" => "215", /* multiplication sign, U+00D7 ISOnum  */
                           "Oslash" => "216", /* latin capital letter O with stroke = latin capital letter O slash, U+00D8 ISOlat1 */
                           "Ugrave" => "217", /* latin capital letter U with grave,U+00D9 ISOlat1  */
                           "Uacute" => "218", /* latin capital letter U with acute,U+00DA ISOlat1  */
                           "Ucirc" => "219", /* latin capital letter U with circumflex,U+00DB ISOlat1  */
                           "Uuml" => "220", /* latin capital letter U with diaeresis,U+00DC ISOlat1  */
                           "Yacute" => "221", /* latin capital letter Y with acute,U+00DD ISOlat1  */
                           "THORN" => "222", /* latin capital letter THORN,U+00DE ISOlat1  */
                           "szlig" => "223", /* latin small letter sharp s = ess-zed,U+00DF ISOlat1  */
                           "agrave" => "224", /* latin small letter a with grave = latin small letter a grave, U+00E0 ISOlat1 */
                           "aacute" => "225", /* latin small letter a with acute,U+00E1 ISOlat1  */
                           "acirc" => "226", /* latin small letter a with circumflex,U+00E2 ISOlat1  */
                           "atilde" => "227", /* latin small letter a with tilde,U+00E3 ISOlat1  */
                           "auml" => "228", /* latin small letter a with diaeresis,U+00E4 ISOlat1  */
                           "aring" => "229", /* latin small letter a with ring above = latin small letter a ring, U+00E5 ISOlat1 */
                           "aelig" => "230", /* latin small letter ae = latin small ligature ae, U+00E6 ISOlat1 */
                           "ccedil" => "231", /* latin small letter c with cedilla,U+00E7 ISOlat1  */
                           "egrave" => "232", /* latin small letter e with grave,U+00E8 ISOlat1  */
                           "eacute" => "233", /* latin small letter e with acute,U+00E9 ISOlat1  */
                           "ecirc" => "234", /* latin small letter e with circumflex,U+00EA ISOlat1  */
                           "euml" => "235", /* latin small letter e with diaeresis,U+00EB ISOlat1  */
                           "igrave" => "236", /* latin small letter i with grave,U+00EC ISOlat1  */
                           "iacute" => "237", /* latin small letter i with acute,U+00ED ISOlat1  */
                           "icirc" => "238", /* latin small letter i with circumflex,U+00EE ISOlat1  */
                           "iuml" => "239", /* latin small letter i with diaeresis,U+00EF ISOlat1  */
                           "eth" => "240", /* latin small letter eth, U+00F0 ISOlat1  */
                           "ntilde" => "241", /* latin small letter n with tilde,U+00F1 ISOlat1  */
                           "ograve" => "242", /* latin small letter o with grave,U+00F2 ISOlat1  */
                           "oacute" => "243", /* latin small letter o with acute,U+00F3 ISOlat1  */
                           "ocirc" => "244", /* latin small letter o with circumflex,U+00F4 ISOlat1  */
                           "otilde" => "245", /* latin small letter o with tilde,U+00F5 ISOlat1  */
                           "ouml" => "246", /* latin small letter o with diaeresis,U+00F6 ISOlat1  */
                           "divide" => "247", /* division sign, U+00F7 ISOnum  */
                           "oslash" => "248", /* latin small letter o with stroke, = latin small letter o slash, U+00F8 ISOlat1 */
                           "ugrave" => "249", /* latin small letter u with grave,U+00F9 ISOlat1  */
                           "uacute" => "250", /* latin small letter u with acute,U+00FA ISOlat1  */
                           "ucirc" => "251", /* latin small letter u with circumflex,U+00FB ISOlat1  */
                           "uuml" => "252", /* latin small letter u with diaeresis,U+00FC ISOlat1  */
                           "yacute" => "253", /* latin small letter y with acute,U+00FD ISOlat1  */
                           "thorn" => "254", /* latin small letter thorn,U+00FE ISOlat1  */
                           "yuml" => "255", /* latin small letter y with diaeresis,U+00FF ISOlat1  */
                           /* C0 Controls and Basic Latin  */
                           "quot" => "34",   /* quotation mark = APL quote,U+0022 ISOnum  */
                           "amp" => "38",   /* ampersand, U+0026 ISOnum  */
                           "lt" => "60",   /* less-than sign, U+003C ISOnum  */
                           "gt" => "62",   /* greater-than sign, U+003E ISOnum  */
                           /* Latin Extended-A  */
                           "OElig" => "338",  /* latin capital ligature OE,U+0152 ISOlat2  */
                           "oelig" => "339",  /* latin small ligature oe, U+0153 ISOlat2  */
                           /* ligature is a misnomer, this is a separate character in some languages  */
                           "Scaron" => "352",  /* latin capital letter S with caron,U+0160 ISOlat2  */
                           "scaron" => "353",  /* latin small letter s with caron,U+0161 ISOlat2  */
                           "Yuml" => "376",  /* latin capital letter Y with diaeresis,U+0178 ISOlat2  */
                           /* Spacing Modifier Letters  */
                           "circ" => "710",  /* modifier letter circumflex accent,U+02C6 ISOpub  */
                           "tilde" => "732",  /* small tilde, U+02DC ISOdia  */
                           /* General Punctuation  */
                           "ensp" => "8194", /* en space, U+2002 ISOpub  */
                           "emsp" => "8195", /* em space, U+2003 ISOpub  */
                           "thinsp" => "8201", /* thin space, U+2009 ISOpub  */
                           "zwnj" => "8204", /* zero width non-joiner,U+200C NEW RFC 2070  */
                           "zwj" => "8205", /* zero width joiner, U+200D NEW RFC 2070  */
                           "lrm" => "8206", /* left-to-right mark, U+200E NEW RFC 2070  */
                           "rlm" => "8207", /* right-to-left mark, U+200F NEW RFC 2070  */
                           "ndash" => "8211", /* en dash, U+2013 ISOpub  */
                           "mdash" => "8212", /* em dash, U+2014 ISOpub  */
                           "lsquo" => "8216", /* left single quotation mark,U+2018 ISOnum  */
                           "rsquo" => "8217", /* right single quotation mark,U+2019 ISOnum  */
                           "sbquo" => "8218", /* single low-9 quotation mark, U+201A NEW  */
                           "ldquo" => "8220", /* left double quotation mark,U+201C ISOnum  */
                           "rdquo" => "8221", /* right double quotation mark,U+201D ISOnum  */
                           "bdquo" => "8222", /* double low-9 quotation mark, U+201E NEW  */
                           "dagger" => "8224", /* dagger, U+2020 ISOpub  */
                           "Dagger" => "8225", /* double dagger, U+2021 ISOpub  */
                           "permil" => "8240", /* per mille sign, U+2030 ISOtech  */
                           "lsaquo" => "8249", /* single left-pointing angle quotation mark,U+2039 ISO proposed  */
                           /* lsaquo is proposed but not yet ISO standardized  */
                           "rsaquo" => "8250", /* single right-pointing angle quotation mark,U+203A ISO proposed  */
                           /* rsaquo is proposed but not yet ISO standardized  */
                           "euro" => "8364", /* euro sign, U+20AC NEW  */
                           /* Latin Extended-B  */
                           "fnof" => "402", /* latin small f with hook = function = florin, U+0192 ISOtech */
                           /* Greek  */
                           "Alpha" => "913", /* greek capital letter alpha, U+0391  */
                           "Beta" => "914", /* greek capital letter beta, U+0392  */
                           "Gamma" => "915", /* greek capital letter gamma,U+0393 ISOgrk3  */
                           "Delta" => "916", /* greek capital letter delta,U+0394 ISOgrk3  */
                           "Epsilon" => "917", /* greek capital letter epsilon, U+0395  */
                           "Zeta" => "918", /* greek capital letter zeta, U+0396  */
                           "Eta" => "919", /* greek capital letter eta, U+0397  */
                           "Theta" => "920", /* greek capital letter theta,U+0398 ISOgrk3  */
                           "Iota" => "921", /* greek capital letter iota, U+0399  */
                           "Kappa" => "922", /* greek capital letter kappa, U+039A  */
                           "Lambda" => "923", /* greek capital letter lambda,U+039B ISOgrk3  */
                           "Mu" => "924", /* greek capital letter mu, U+039C  */
                           "Nu" => "925", /* greek capital letter nu, U+039D  */
                           "Xi" => "926", /* greek capital letter xi, U+039E ISOgrk3  */
                           "Omicron" => "927", /* greek capital letter omicron, U+039F  */
                           "Pi" => "928", /* greek capital letter pi, U+03A0 ISOgrk3  */
                           "Rho" => "929", /* greek capital letter rho, U+03A1  */
                           /* there is no Sigmaf, and no U+03A2 character either  */
                           "Sigma" => "931", /* greek capital letter sigma,U+03A3 ISOgrk3  */
                           "Tau" => "932", /* greek capital letter tau, U+03A4  */
                           "Upsilon" => "933", /* greek capital letter upsilon,U+03A5 ISOgrk3  */
                           "Phi" => "934", /* greek capital letter phi,U+03A6 ISOgrk3  */
                           "Chi" => "935", /* greek capital letter chi, U+03A7  */
                           "Psi" => "936", /* greek capital letter psi,U+03A8 ISOgrk3  */
                           "Omega" => "937", /* greek capital letter omega,U+03A9 ISOgrk3  */
                           "alpha" => "945", /* greek small letter alpha,U+03B1 ISOgrk3  */
                           "beta" => "946", /* greek small letter beta, U+03B2 ISOgrk3  */
                           "gamma" => "947", /* greek small letter gamma,U+03B3 ISOgrk3  */
                           "delta" => "948", /* greek small letter delta,U+03B4 ISOgrk3  */
                           "epsilon" => "949", /* greek small letter epsilon,U+03B5 ISOgrk3  */
                           "zeta" => "950", /* greek small letter zeta, U+03B6 ISOgrk3  */
                           "eta" => "951", /* greek small letter eta, U+03B7 ISOgrk3  */
                           "theta" => "952", /* greek small letter theta,U+03B8 ISOgrk3  */
                           "iota" => "953", /* greek small letter iota, U+03B9 ISOgrk3  */
                           "kappa" => "954", /* greek small letter kappa,U+03BA ISOgrk3  */
                           "lambda" => "955", /* greek small letter lambda,U+03BB ISOgrk3  */
                           "mu" => "956", /* greek small letter mu, U+03BC ISOgrk3  */
                           "nu" => "957", /* greek small letter nu, U+03BD ISOgrk3  */
                           "xi" => "958", /* greek small letter xi, U+03BE ISOgrk3  */
                           "omicron" => "959", /* greek small letter omicron, U+03BF NEW  */
                           "pi" => "960", /* greek small letter pi, U+03C0 ISOgrk3  */
                           "rho" => "961", /* greek small letter rho, U+03C1 ISOgrk3  */
                           "sigmaf" => "962", /* greek small letter final sigma,U+03C2 ISOgrk3  */
                           "sigma" => "963", /* greek small letter sigma,U+03C3 ISOgrk3  */
                           "tau" => "964", /* greek small letter tau, U+03C4 ISOgrk3  */
                           "upsilon" => "965", /* greek small letter upsilon,U+03C5 ISOgrk3  */
                           "phi" => "966", /* greek small letter phi, U+03C6 ISOgrk3  */
                           "chi" => "967", /* greek small letter chi, U+03C7 ISOgrk3  */
                           "psi" => "968", /* greek small letter psi, U+03C8 ISOgrk3  */
                           "omega" => "969", /* greek small letter omega,U+03C9 ISOgrk3  */
                           "thetasym" => "977", /* greek small letter theta symbol,U+03D1 NEW  */
                           "upsih" => "978", /* greek upsilon with hook symbol,U+03D2 NEW  */
                           "piv" => "982", /* greek pi symbol, U+03D6 ISOgrk3  */
                           /* General Punctuation  */
                           "bull" => "8226", /* bullet = black small circle,U+2022 ISOpub   */
                           /* bullet is NOT the same as bullet operator, U+2219  */
                           "hellip" => "8230", /* horizontal ellipsis = three dot leader,U+2026 ISOpub   */
                           "prime" => "8242", /* prime = minutes = feet, U+2032 ISOtech  */
                           "Prime" => "8243", /* double prime = seconds = inches,U+2033 ISOtech  */
                           "oline" => "8254", /* overline = spacing overscore,U+203E NEW  */
                           "frasl" => "8260", /* fraction slash, U+2044 NEW  */
                           /* Letterlike Symbols  */
                           "weierp" => "8472", /* script capital P = power set = Weierstrass p, U+2118 ISOamso */
                           "image" => "8465", /* blackletter capital I = imaginary part,U+2111 ISOamso  */
                           "real" => "8476", /* blackletter capital R = real part symbol,U+211C ISOamso  */
                           "trade" => "8482", /* trade mark sign, U+2122 ISOnum  */
                           "alefsym" => "8501", /* alef symbol = first transfinite cardinal,U+2135 NEW  */
                           /* alef symbol is NOT the same as hebrew letter alef, U+05D0 although the same glyph could be used to depict both characters */
                           /* Arrows  */
                           "larr" => "8592", /* leftwards arrow, U+2190 ISOnum  */
                           "uarr" => "8593", /* upwards arrow, U+2191 ISOnum */
                           "rarr" => "8594", /* rightwards arrow, U+2192 ISOnum  */
                           "darr" => "8595", /* downwards arrow, U+2193 ISOnum  */
                           "harr" => "8596", /* left right arrow, U+2194 ISOamsa  */
                           "crarr" => "8629", /* downwards arrow with corner leftwards = carriage return, U+21B5 NEW */
                           "lArr" => "8656", /* leftwards double arrow, U+21D0 ISOtech  */
                           /* ISO 10646 does not say that lArr is the same as the 'is implied by' arrow but also does not have any other character for that function. So ? lArr can be used for 'is implied by' as ISOtech suggests */
                           "uArr" => "8657", /* upwards double arrow, U+21D1 ISOamsa  */
                           "rArr" => "8658", /* rightwards double arrow,U+21D2 ISOtech  */
                           /* ISO 10646 does not say this is the 'implies' character but does not have another character with this function so rArr can be used for 'implies' as ISOtech suggests */
                           "dArr" => "8659", /* downwards double arrow, U+21D3 ISOamsa  */
                           "hArr" => "8660", /* left right double arrow,U+21D4 ISOamsa  */
                           /* Mathematical Operators  */
                           "forall" => "8704", /* for all, U+2200 ISOtech  */
                           "part" => "8706", /* partial differential, U+2202 ISOtech   */
                           "exist" => "8707", /* there exists, U+2203 ISOtech  */
                           "empty" => "8709", /* empty set = null set = diameter,U+2205 ISOamso  */
                           "nabla" => "8711", /* nabla = backward difference,U+2207 ISOtech  */
                           "isin" => "8712", /* element of, U+2208 ISOtech  */
                           "notin" => "8713", /* not an element of, U+2209 ISOtech  */
                           "ni" => "8715", /* contains as member, U+220B ISOtech  */
                           /* should there be a more memorable name than 'ni'?  */
                           "prod" => "8719", /* n-ary product = product sign,U+220F ISOamsb  */
                           /* prod is NOT the same character as U+03A0 'greek capital letter pi' though the same glyph might be used for both */
                           "sum" => "8721", /* n-ary sumation, U+2211 ISOamsb  */
                           /* sum is NOT the same character as U+03A3 'greek capital letter sigma' though the same glyph might be used for both */
                           "minus" => "8722", /* minus sign, U+2212 ISOtech  */
                           "lowast" => "8727", /* asterisk operator, U+2217 ISOtech  */
                           "radic" => "8730", /* square root = radical sign,U+221A ISOtech  */
                           "prop" => "8733", /* proportional to, U+221D ISOtech  */
                           "infin" => "8734", /* infinity, U+221E ISOtech  */
                           "ang" => "8736", /* angle, U+2220 ISOamso  */
                           "and" => "8743", /* logical and = wedge, U+2227 ISOtech  */
                           "or" => "8744", /* logical or = vee, U+2228 ISOtech  */
                           "cap" => "8745", /* intersection = cap, U+2229 ISOtech  */
                           "cup" => "8746", /* union = cup, U+222A ISOtech  */
                           "int" => "8747", /* integral, U+222B ISOtech  */
                           "there4" => "8756", /* therefore, U+2234 ISOtech  */
                           "sim" => "8764", /* tilde operator = varies with = similar to,U+223C ISOtech  */
                           /* tilde operator is NOT the same character as the tilde, U+007E, although the same glyph might be used to represent both  */
                           "cong" => "8773", /* approximately equal to, U+2245 ISOtech  */
                           "asymp" => "8776", /* almost equal to = asymptotic to,U+2248 ISOamsr  */
                           "ne" => "8800", /* not equal to, U+2260 ISOtech  */
                           "equiv" => "8801", /* identical to, U+2261 ISOtech  */
                           "le" => "8804", /* less-than or equal to, U+2264 ISOtech  */
                           "ge" => "8805", /* greater-than or equal to,U+2265 ISOtech  */
                           "sub" => "8834", /* subset of, U+2282 ISOtech  */
                           "sup" => "8835", /* superset of, U+2283 ISOtech  */
                           /* note that nsup, 'not a superset of, U+2283' is not covered by the Symbol font encoding and is not included. Should it be, for symmetry? It is in ISOamsn  */
                           "nsub" => "8836", /* not a subset of, U+2284 ISOamsn  */
                           "sube" => "8838", /* subset of or equal to, U+2286 ISOtech  */
                           "supe" => "8839", /* superset of or equal to,U+2287 ISOtech  */
                           "oplus" => "8853", /* circled plus = direct sum,U+2295 ISOamsb  */
                           "otimes" => "8855", /* circled times = vector product,U+2297 ISOamsb  */
                           "perp" => "8869", /* up tack = orthogonal to = perpendicular,U+22A5 ISOtech  */
                           "sdot" => "8901", /* dot operator, U+22C5 ISOamsb  */
                           /* dot operator is NOT the same character as U+00B7 middle dot  */
                           /* Miscellaneous Technical  */
                           "lceil" => "8968", /* left ceiling = apl upstile,U+2308 ISOamsc   */
                           "rceil" => "8969", /* right ceiling, U+2309 ISOamsc   */
                           "lfloor" => "8970", /* left floor = apl downstile,U+230A ISOamsc   */
                           "rfloor" => "8971", /* right floor, U+230B ISOamsc   */
                           "lang" => "9001", /* left-pointing angle bracket = bra,U+2329 ISOtech  */
                           /* lang is NOT the same character as U+003C 'less than' or U+2039 'single left-pointing angle quotation mark' */
                           "rang" => "9002", /* right-pointing angle bracket = ket,U+232A ISOtech  */
                           /* rang is NOT the same character as U+003E 'greater than' or U+203A 'single right-pointing angle quotation mark' */
                           /* Geometric Shapes  */
                           "loz" => "9674", /* lozenge, U+25CA ISOpub  */
                           /* Miscellaneous Symbols  */
                           "spades" => "9824", /* black spade suit, U+2660 ISOpub  */
                           /* black here seems to mean filled as opposed to hollow  */
                           "clubs" => "9827", /* black club suit = shamrock,U+2663 ISOpub  */
                           "hearts" => "9829", /* black heart suit = valentine,U+2665 ISOpub  */
                           "diams" => "9830", /* black diamond suit, U+2666 ISOpub  */
                           );
