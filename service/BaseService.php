<?php
abstract class BaseService
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    function __construct(&$em)
    {
        $this->em = $em;
    }

}
