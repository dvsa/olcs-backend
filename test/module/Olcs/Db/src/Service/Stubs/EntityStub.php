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

    public $parent;

    public $cousin;

    public $data;

    public function getId()
    {
        return $this->id;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function setCousin($cousin)
    {
        $this->cousin = $cousin;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
