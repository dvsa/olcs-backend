<?php

/**
 * MockEntity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service\Stubs;

/**
 * MockEntity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EntityStub
{
    protected $id;

    protected $address;

    protected $name;

    public function getId()
    {
        return $this->id;
    }
}
