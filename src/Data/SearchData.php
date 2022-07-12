<?php

namespace App\Data;

use App\Entity\Type;


class SearchData
{
    /**
     *
     * @var integer
     */
    public $page = 1;
    
    /**
     * @var String
     */
    public $q='';

    /**
     * @var Type[]
     */
    public $type =[];

    /**
     * 
     * @var null|integer
     */
    public $max;

    /**
     * 
     * @var null|integer
     */
    public $min;
}