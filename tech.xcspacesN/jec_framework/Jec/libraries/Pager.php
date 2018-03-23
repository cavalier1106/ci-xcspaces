<?php
/**
 * @copyright Jec
 * @link jecelyin@gmail.com
 * @author jecelyin peng
 * @license 转载或修改请保留版权信息
 */
class Pager
{
    /**
     * $query 查询语句
     * $pageRows 每页显示行数
     * $url 地址: list-{PAGE}.html
     * @uses $page = new Pager(array(
     * 'url' => '',
     * 'query' => '',,
     * ));
     * example:
     * Template:{echo $page->display()}
     *
     **/
    public $pageRows = 30; //每页显示行数

    public $url = '';
    public $file_rule = '';
    public $list_rule = '';

    public $page = 0; //指定当前页，不指定将取提交的page参数
    private $totalPage = 0; //总页数
    public $totalRows = 0; //记录总数
    public $style = 'i'; //风格 default
    public $left = 2; //左边显示个数
    public $right = 7; //显示右边列表的页的个数，如 1..3 4 5
    private static $styleFile = ''; //为当前程序默认一个全局默认的样式
    private $req = array();
    public $formName = '0'; //表单名称，数字则表示第一个表单。默认为表单提交方式
    private $isLinkByJS = false; //是否使用JS来提交表单

    public function __construct($param = array())
    {
        if (isset($param['page']))
        {
            $page = (int)$param['page'];
        } else
        {
            $this->page ? $page = $this->page : $page = Jec::getInt('page');
        }

        //处理参数
        foreach ($param as $key => $val)
        {
            $this->$key = $val;
        }
        unset($param);
        $this->page = $page;
        if (!is_numeric($this->page))
        {
            $this->page = 1;
        }
        if ($this->page < 1)
        {
            $this->page = 1;
        }

        if ($this->pageRows < 1)
        {
            return;
        }
    }

    /**
     * 设置总行数
     * @param $num
     */
    public function setTotalRows($num)
    {
        $this->totalRows = $num;
    }

    /**
     * 提供数据库分页偏移，limit offset, pageRows
     * @return int
     */
    public function getOffset()
    {
        return ($this->page - 1) * $this->pageRows;
    }

    /**
     * 返回每页行数
     * @return int
     */
    public function getLimit()
    {
        return (int)$this->pageRows;
    }

    private function getUrl($page)
    {
        if ($this->url && $this->list_rule && $this->file_rule)
        { //静态
            $url = $page == 1 ? $this->url . $this->file_rule : $this->url . $this->list_rule;
            $url = str_replace('{PAGE}', $page, $url);
        } elseif ($this->url)
        { //动态
            $url = strpos($this->url, '?') === false ? $this->url . "?page=$page" : $this->url . "&page=$page";
        } else
        {
            if($this->isLinkByJS)
                return "javascript:JecPagerLink({$page});";

            if (!$this->req)
            {
                $this->req = $_GET;
                foreach ($_POST as $key => $val)
                {
                    $this->req[$key] = $val;
                }
                unset($this->req['page']);
            }

            $url = '?' . http_build_query($this->req) . "&page=$page";
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getLinkJavascript()
    {
        if($this->url)
            return '';

        $this->isLinkByJS = true;
        $obj = is_numeric($this->formName) ? "document.forms[{$this->formName}]" : "document.form['{$this->formName}']";
        return <<<EOT
<script type="text/javascript">
var __form = $obj;
$(document).ready(function(){
    $('input[name=page]').keyup(function(e) {
      if (e.keyCode == 13) {
         e.preventDefault();
         var page = this.value;
         setTimeout(function(){JecPagerLink(page);},10);
       }
    });
});
function JecPagerLink(page)
{
    if(!__form['page'])$(__form).append('<input type="hidden" name="page" value="0" />');
    __form['page'].value = page;
    __form.submit();
}
</script>
EOT;
    }

    /**
     * 获取 1 2 3 4 5 6 7 8 9 10 ..16 这样的列表的左页数，右页数
     * @return array (curPage, left, right)
     */
    private function get_page_list()
    {
        $curPage   = $this->page; //当前页
        $totalRows = $this->totalRows; //总行数
        $totalPage = $this->totalPage; //总页数
        if ($curPage > $totalPage)
        {
            $curPage = $totalPage;
            $this->page = 1;
        }
        $show_right_num = $this->right;
        $show_left_num  = $this->left; //左边页的个数
        $show_total_num = $show_left_num + $show_right_num + 1;
        if ($show_total_num > $totalPage)
        {
            $left  = 1;
            $right = $totalPage;
        } else
        {
            if ($curPage - $show_left_num > 1)
            {
                $left = $curPage - $show_left_num;
            } else
            {
                $left           = 1;
                $show_right_num = $show_left_num - $curPage + $show_right_num +
                    1;
            }
            $right = $curPage + $show_right_num;
            if ($right >= $totalPage)
            {
                //如果右页数大于总页数，右页数肯定为总页数
                $right = $totalPage;
                //保证要显示的页的个数
                $left = $totalPage - $show_total_num + 1 >
                    1 ? $totalPage - $show_total_num + 1 : 1;
            }
        } //end if
        return array('curPage' => $curPage, 'left' => $left,
                     'right'   => $right);
    }

    /**
     * @static
     * 设置全局样式
     * @param $file 样式文件完整路径
     */
    public static function setDefaultStyleFile($file)
    {
        self::$styleFile = $file;
    }

    /**
     * @return int 返回总页数
     */
    public function getTotalPage()
    {
        $totalPage = ceil($this->totalRows / $this->pageRows);
        if ($this->totalPage && $totalPage > $this->totalPage)
        {
            $totalPage = $this->totalPage;
        }
        $this->totalPage = $totalPage;
        return $totalPage;
    }

    /**
     * @return string 返回分页html内容
     */
    public function render()
    {
        $this->totalPage = $this->getTotalPage();
        $stylefile       = self::$styleFile ? self::$styleFile : LIB_PATH . '/Pager/styles/' . $this->style . '.php';

        return require($stylefile);
    }
    //end class
}
