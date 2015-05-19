<?php

namespace OlcsTest\Db\Controller\CompaniesHouse;

use Olcs\Db\Controller\CompaniesHouse\QueueController;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Zend\Http\Response;

/**
 * Tests Companies House Queue Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class QueueControllerTest extends MockeryTestCase
{
    protected $sm;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = m::mock('\Olcs\Db\Controller\CompaniesHouse\QueueController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testCreateWithResponse()
    {
        $data = 'JSON';
        $this->sut
            ->shouldReceive('formatDataFromJson')
            ->once()
            ->with($data)
            ->andReturn(new Response);

        $this->assertInstanceOf('\Zend\Http\Response', $this->sut->create($data));
    }

    public function testCreateWithData()
    {
        $data = ['type' => 'foo'];
        $count = 99;

        $mockService = m::mock();
        $this->sm->setService('CompaniesHouse/Queue', $mockService);

        $mockService
            ->shouldReceive('enqueueActiveOrganisations')
            ->with('foo')
            ->once()
            ->andReturn($count);

        $this->sut
            ->shouldReceive('formatDataFromJson')
            ->once()
            ->andReturn($data)
            ->shouldReceive('respond')
            ->with(Response::STATUS_CODE_201, 'Queue Populated', $count)
            ->once()
            ->andReturn('response');

        $this->assertEquals('response', $this->sut->create($data));
    }
}
