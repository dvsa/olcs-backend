<?php

/**
 * Scanning Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Scanning\Service;

use Dvsa\Olcs\Scanning\Service\ScanningService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Scanning Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScanningServiceTest extends MockeryTestCase
{
    protected function setup()
    {
        $this->sut = new ScanningService();
    }

    public function testSetDataFromRequest()
    {
        $request = m::mock('\Zend\Http\Request')->makePartial();

        $request->shouldReceive('getPost')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn(['foo' => 1])
                ->getMock()
            )
            ->shouldReceive('getFiles')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn(['bar' => 2])
                ->getMock()
            );

        $this->sut->setDataFromRequest($request);

        $this->assertEquals(
            [
                'foo' => 1,
                'bar' => 2
            ],
            $this->sut->getData()
        );
    }

    public function testIsValidRequestWithNoImage()
    {
        $request = m::mock('\Zend\Http\Request')->makePartial();

        $request->shouldReceive('getPost')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn([])
                ->getMock()
            )
            ->shouldReceive('getFiles')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn([])
                ->getMock()
            );

        $this->sut->setDataFromRequest($request);

        $this->assertFalse($this->sut->isValidRequest());
    }

    public function testIsValidRequestWithNoDescription()
    {
        $request = m::mock('\Zend\Http\Request')->makePartial();

        $request->shouldReceive('getPost')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn([])
                ->getMock()
            )
            ->shouldReceive('getFiles')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn(
                    [
                        'image' => [
                            'error' => 0
                        ]
                    ]
                )
                ->getMock()
            );

        $this->sut->setDataFromRequest($request);

        $this->assertFalse($this->sut->isValidRequest());
    }

    public function testIsValidRequestWithValidData()
    {
        $request = m::mock('\Zend\Http\Request')->makePartial();

        $request->shouldReceive('getPost')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn(['description' => 123])
                ->getMock()
            )
            ->shouldReceive('getFiles')
            ->andReturn(
                m::mock()
                ->shouldReceive('toArray')
                ->andReturn(
                    [
                        'image' => [
                            'error' => 0
                        ]
                    ]
                )
                ->getMock()
            );

        $this->sut->setDataFromRequest($request);

        $this->assertTrue($this->sut->isValidRequest());
    }
}
