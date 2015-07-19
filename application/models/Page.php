<?php
/**
 * 获取和修改用户相关信息
 *
 */
class PageModel
{
    private $total;      //总记录
    private $pagesize;    //每页显示多少条
    private $page;           //当前页码
    private $pagenum;      //总页码
    private $url;           //地址f
    private $bothnum;      //两边保持数字分页的量

    //构造方法初始化
    public function __construct($_url,$_total, $_page,$_pagesize,$_sep='?page=')
    {
        $this->total    = $_total ? $_total : 1;
        $this->pagesize = $_pagesize;
        $this->pagenum  = ceil($this->total / $this->pagesize);
        $this->page     = $_page;
        $this->url      = $_url;
        $this->bothnum  = 3;
        $this->sep      = $_sep;
    }

    //拦截器
    public function __get($_key)
    {
        return $this->$_key;
    }

    //数字目录
    private function pageList()
    {
        $_pagelist = '';
        for ($i=$this->bothnum;$i>=1;$i--) {
            $_page = $this->page-$i;
            if ($_page < 1) continue;
            $url = $this->url.$this->sep.$_page;
            $pagestr = "<li><a href=\"{$url}\">{$_page}</a></li>";
            $_pagelist .= $pagestr;
        }
        $pagestr = "<li class=\"active\"><a href=\"#\">{$this->page}</a></li>";
        $_pagelist .= $pagestr;
        for ($i=1;$i<=$this->bothnum;$i++) {
            $_page = $this->page+$i;
            if ($_page > $this->pagenum) break;
            $url = $this->url.$this->sep.$_page;
            $pagestr = "<li><a href=\"{$url}\">{$_page}</a></li>";
            $_pagelist .= $pagestr;
        }
        return $_pagelist;
    }

    //首页
    private function first()
    {
        if ($this->page > $this->bothnum+1) {
            $pagestr = "<li><a href=\"{$this->url}/1\">1</a></li>";
            $skip = "<li class=\"disabled\"><span>...</span></li>";
            $pagestr = $pagestr . $skip;
            return $pagestr;
        }
    }

    //上一页
    private function prev()
    {
        if ($this->page == 1) {
            $pagestr = "<li class=\"disabled\"><a href=\"#\">«</a></li>";
        } else {
            $url = $this->url.$this->sep.($this->page-1);
            $pagestr = "<li><a href=\"{$url}\">«</a></li>";
        }
        return $pagestr;
    }

    private function next()
    {
        if ($this->page == $this->pagenum) {
            $pagestr = "<li class=\"disabled\"><a href=\"#\">»</a></li>";
        } else {
            $url = $this->url.$this->sep.($this->page+1);
            $pagestr = "<li><a href=\"{$url}\">»</a></li>";
        }
        return $pagestr;
    }

    //尾页
    private function last()
    {
        if ($this->pagenum - $this->page > $this->bothnum) {
            // return ' ...<a href="'.$this->url.$this->sep.$this->pagenum.'">'.$this->pagenum.'</a> ';
            $url = $this->url.$this->sep.($this->pagenum);
            $pagestr = "<li><a href=\"{$url}\">{$this->pagenum}</a></li>";
            $skip = "<li class=\"disabled\"><span>...</span></li>";
            $pagestr = $skip . $pagestr;
            return $pagestr;
        }
    }

    //分页信息
    public function showpage()
    {
        $_page = '<ul class="pagination">';
        $_page .= $this->prev();
        $_page .= $this->first();
        $_page .= $this->pageList();
        $_page .= $this->last();
        $_page .= $this->next();
        $_page .= '</ul>';
        return $_page;
    }

}
