<?php

namespace OlcsTest\Db\Service\CompaniesHouse;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Olcs\Db\Service\CompaniesHouse\Queue as Service;
use OlcsTest\Bootstrap;

/**
 * Class QueueTest
 * @package OlcsTest\Db\Service\CompaniesHouse
 */
class QueueTest extends MockeryTestCase
{
    protected $sm;

    public function setUp()
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new Service($this->sm);
    }

    public function testEnqueueActiveOrganisations()
    {
        $type = 'que_typ_foo';
        $count = 99;

        $mockEntityManager = m::mock();
        $this->sm->setService('doctrine.entitymanager.orm_default', $mockEntityManager);

        $mockConnection = m::mock();

        $mockStatement = m::mock();

        $mockEntityManager
            ->shouldReceive('getConnection')
            ->once()
            ->andReturn($mockConnection);

        $mockConnection
            ->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement
            ->shouldReceive('execute')
            ->once()
            ->with(array($type))
            ->andReturn(true)
            ->shouldReceive('rowCount')
            ->andReturn($count);

        $this->sut->enqueueActiveOrganisations($type);
    }
}
