<?php


namespace c006\pagination\assets;


use Yii;

class AppPagination
{

    public $page;
    private $_conn;
    private $_limit;
    private $_page;
    private $_query;
    private $_total;


    function __construct($query, $pages_shown)
    {
        $this->_conn = Yii::$app->getDb();
        $this->_query = $query;
        $this->page = new ResultsPager();

        $results = $this->_conn->createCommand($this->_query)->queryAll();
        $this->page->all_data = $results;
        $this->page->total = sizeof($results);
        $this->page->pages_shown = $pages_shown;
    }


    public function getData($limit = 10, $page = 1)
    {

        $limit = preg_replace('/[^0-9]/', '', $limit);
        $page = preg_replace('/[^0-9]/', '', $page);

        if ($this->_limit == '0') {
            $query = $this->_query;
        } else {
            $query = $this->_query . " LIMIT " . (($page - 1) * $limit) . ", $limit";
        }

        $results = $this->_conn->createCommand($query)->queryAll();

        $this->page->page = (int)$page;
        $this->page->limit = (int)$limit;
        $this->page->data = $results;
    }


    public function createLinks($querystring = '')
    {
        if ($this->page->limit == '0') {
            return '';
        }

        $last = ceil($this->page->total / $this->page->limit);

        $start = ($this->page->page - ($this->page->pages_shown + 2) < 1) ? 1 : $this->page->page - ($this->page->pages_shown + 2);
        $end = ($this->page->page + $this->page->pages_shown < $last) ? $this->page->page + $this->page->pages_shown : $last;





        $html = '<div class="table" style="width: auto">';

        $class = ($this->page->page == 1) ? "hide" : "";
        $html .= '<div class="table-cell ' . $class . '"><a href="?limit=' . $this->page->limit . '&page=' . ($this->page->page - 1) . $querystring . '">&laquo;</a></div>';

        if ($start > 1) {
            $html .= '<div class="table-cell"><a href="?limit=' . $this->page->limit . '&page=1' . $querystring . '">1</a></div>';
            $html .= '<div class="table-cell"><span>...</span></div>';
        }


        for ($i = $start; $i <= $this->page->pages_shown + 2; $i++) {
            $class = ($this->page->page == $i) ? "paging-current" : "";
            $html .= '<div class="table-cell ' . $class . '"><a href="?limit=' . $this->page->limit . '&page=' . $i . $querystring . '">' . $i . '</a></div>';
        }

        if ($end < $last) {
            $html .= '<div class="table-cell"><span>...</span></div>';
            $html .= '<div class="table-cell"><a href="?limit=' . $this->page->limit . '&page=' . $last . $querystring . '">' . $last . '</a></div>';
        }

        $class = ($this->page->page == $last) ? "hide" : "";
        $html .= '<div class="table-cell ' . $class . '"><a href="?limit=' . $this->page->limit . '&page=' . ($this->page->page + 1) . $querystring . '">&raquo;</a></div>';

        $html .= '</div>';

        return $html;
    }


}

class ResultsPager
{

    public $page;
    public $limit;
    public $total;
    public $data;
    public $all_data;
    public $pages_shown;

}