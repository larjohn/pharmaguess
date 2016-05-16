<?php
namespace  App;

/**
 * Created by PhpStorm.
 * User: larjo
 * Date: 15/5/2016
 * Time: 3:30 μμ
 */
class Triple
{
    /**
     * @var string
     */
    public $subject;
    /**
     * @var string
     */
    public $predicate;
    /**
     * @var string
     */
    public $object;

    public function __construct(string $subject, string $predicate, string $object)
    {

        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;
    }

}