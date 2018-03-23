<?php
/**
 * Pager分页器样式
 * @return $css
 * @param $totalPage 总页数
 * @param $totalRows 总行数
 */

//根据左页数，右页数获取相关参数
$params = $this->get_page_list();
$left = $params['left'];
$right = $params['right'];

$curPage = $this->page; //当前页
$totalRows = $this->totalRows; //总行数
$totalPage = $this->totalPage; //总页数

//if($totalPage < 2)return '';
$css = '';
if ($left != 1)
    $css .= "<a href=\"" . $this->getUrl(1) . "\" class=\"first\">|&lt;</a>";
if ($curPage - 1)
    $css .= "<a href=\"" . $this->getUrl($curPage - 1) . "\" class=\"prev\">&lt;</a>";

for ($i = $left; $i <= $right; $i++) {
    if ($curPage == $i)
        $css .= "<span class=\"curPage\">{$curPage}</span>";
    else
        $css .= "<a href=\"" . $this->getUrl($i) . "\">{$i}</a>";
}

if ($curPage < $totalPage)
    $css .= "<a href=\"" . $this->getUrl($curPage + 1) . "\" class=\"next\">&gt;</a>";
//if ($right != $totalPage)
//    $css .= "<a href=\"" . $this->getUrl($totalPage) . "\" class=\"last\">&gt;|</a>";

return $css;
