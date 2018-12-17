<?php

namespace Dvsa\OlcsTest\Api\Service\Publication;

use ArrayObject;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Service\Publication;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\PublicationGenerator
 */
class PublicationGeneratorTest extends MockeryTestCase
{
    /** @var ServiceLocatorInterface|m\MockInterface */
    private $mockCtxMngr;
    /** @var ServiceLocatorInterface|m\MockInterface */
    private $mockProcessMngr;

    public function setUp()
    {
        $this->mockCtxMngr = m::mock(Publication\Context\PluginManager::class);
        $this->mockProcessMngr = m::mock(Publication\Process\PluginManager::class);
    }

    public function testCreatePublicationOk()
    {
        $cfg = [
            'pubKey' => [
                'context' => [
                    'unit_CtxCls1',
                ],
                'process' => [
                    'unit_ProceeeCls1',
                ],
            ],
        ];

        $expectPub = m::mock(Entity\Publication\PublicationLink::class);
        $expectCtx = ['unit_expectCtx'];

        //  mock context manager
        $mockCtxClass = m::mock()
            ->shouldReceive('provide')->times(1)->with($expectPub, m::type(ArrayObject::class))
            ->getMock();

        $this->mockCtxMngr->shouldReceive('get')->times(1)->with('unit_CtxCls1')->andReturn($mockCtxClass);

        //  mock process manager
        $mockProcessClass = m::mock()
            ->shouldReceive('process')->times(1)->with($expectPub, m::type(ImmutableArrayObject::class))
            ->getMock();

        $this->mockProcessMngr->shouldReceive('get')->times(1)->with('unit_ProceeeCls1')->andReturn($mockProcessClass);

        //  call & check
        $sut = new PublicationGenerator($cfg, $this->mockCtxMngr, $this->mockProcessMngr);
        $actual = $sut->createPublication('pubKey', $expectPub, $expectCtx);

        static::assertEquals($expectPub, $actual);
    }

    public function testCreatePublicationFailInvalidCfg()
    {
        //  expect
        $this->expectException(\Exception::class, 'Invalid publication config');

        //  call
        $sut = new PublicationGenerator([], $this->mockCtxMngr, $this->mockProcessMngr);
        $sut->createPublication('invalidKey', null, null);
    }

    public function testCreatePublicationFailInvalidProcess()
    {
        $cfg = [
            'pubKey' => [
                'context' => [
                    'unit_CtxCls1',
                ],
            ],
        ];

        $expectPub = m::mock(Entity\Publication\PublicationLink::class);
        $expectCtx = ['unit_expectCtx'];

        //  mock context manager
        $mockCtxClass = m::mock();
        $mockCtxClass->shouldReceive('provide');

        $this->mockCtxMngr->shouldReceive('get')->andReturn($mockCtxClass);

        //  expect
        $this->expectException(\Exception::class, 'No publication processors specified');

        //  call
        $sut = new PublicationGenerator($cfg, $this->mockCtxMngr, $this->mockProcessMngr);
        $sut->createPublication('pubKey', $expectPub, $expectCtx);
    }
}
