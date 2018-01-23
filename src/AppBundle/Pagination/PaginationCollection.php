<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20/01/18
 * Time: 21:09
 */

namespace AppBundle\Pagination;
use AppBundle\Annotation\Link;
use Doctrine\Common\Annotations\Reader;


class PaginationCollection
{
    private  $items;
    private  $total;
    private  $count;

    private $_links = array() ;

    /**
     * PaginationCollection constructor.
     * @param $items
     * @param $total
     */
    public function __construct($items, $total)
    {
        $this->items = $items;
        $this->total = $total;
        $this->count = count($items);
    }

    public function addLink($ref,$url)
    {
            $this->_links[$ref]=$url;
    }




}