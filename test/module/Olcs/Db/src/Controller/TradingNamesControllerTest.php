<?php

/**
 * Tests TradingNames
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace OlcsTest\Db\Controller;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Db\Controller\TradingNamesController;
use OlcsTest\Bootstrap;

/**
 * Tests TradingNames
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TradingNamesControllerTest extends MockeryTestCase
{
    /**
     * Setup the controller
     */
    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock(TradingNamesController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @group tradingNamesController
     */
    public function testCreateWithResponse()
    {
        $data = [
            'organisation' => 1
        ];
        $this->sut
            ->shouldReceive('checkMethod')
            ->once()
            ->shouldReceive('formatDataFromJson')
            ->andReturn(new \Zend\Http\Response)
            ->once();

        $this->assertInstanceOf('\Zend\Http\Response', $this->sut->create($data));
    }

    /**
     * @group tradingNamesController
     */
    public function testCreateWithException()
    {
        $data = [
            'organisation' => 1,
            'licence' => 2
        ];
        $this->sut
            ->shouldReceive('checkMethod')
            ->once()
            ->shouldReceive('formatDataFromJson')
            ->andReturn($data)
            ->once()
            ->shouldReceive('getService')
            ->with('TradingName')
            ->andReturn(
                m::mock()
                ->shouldReceive('getList')
                ->andThrow('\Exception')
                ->once()
                ->getMock()
            )
            ->shouldReceive('unknownError')
            ->andReturn('response')
            ->once();

        $this->assertEquals('response', $this->sut->create($data));
    }

    /**
     * @group tradingNamesController
     */
    public function testCreateWithData()
    {
        $data = [
            'organisation' => 1,
            'licence' => 2,
            'tradingNames' => [
                ['name' => 'tn1'],
                ['name' => 'tn2']
            ]
        ];
        $results = [
            'Results' => [
                ['name' => 'tn1'],
                ['name' => 'tn3']
            ]
        ];
        $insert = [
            'organisation' => $data['organisation'],
            'licence' => $data['licence'],
            'name' => 'tn2'
        ];
        $delete = [
            'organisation' => $data['organisation'],
            'licence' => $data['licence'],
            'name' => 'tn3'
        ];

        $this->sut
            ->shouldReceive('checkMethod')
            ->once()
            ->shouldReceive('formatDataFromJson')
            ->andReturn($data)
            ->once()
            ->shouldReceive('getService')
            ->with('TradingName')
            ->andReturn(
                m::mock()
                ->shouldReceive('getList')
                ->with(['organisation' => $data['organisation'], 'licence' => $data['licence']])
                ->andReturn($results)
                ->once()
                ->shouldReceive('create')
                ->with($insert)
                ->once()
                ->shouldReceive('deleteList')
                ->with($delete)
                ->once()
                ->getMock()
            )
            ->shouldReceive('respond')
            ->andReturn('response')
            ->once();
        $this->assertEquals('response', $this->sut->create($data));
    }
}
