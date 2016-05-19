<?php
/**
 * Created by PhpStorm.
 * User: larjo
 * Date: 19/5/2016
 * Time: 3:37 πμ
 */

namespace App;


class Filter extends TriplePatternPart
{

    public function __construct(string $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @var string
     */
    public $filter;

}