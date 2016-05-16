<?php
namespace  App;
/**
 * Created by PhpStorm.
 * User: larjo
 * Date: 15/5/2016
 * Time: 3:29 μμ
 */
class TriplePattern
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var Triple[]
     */
    public $triples;

    /**
     * TriplePattern constructor.
     * @param string $name
     * @param Triple[] $triples
     */
    public function __construct(string $name, array $triples)
    {

        $this->name = $name;
        $this->triples = $triples;
    }

}