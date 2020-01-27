<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory;
use Dvsa\OlcsTest\Api\Service\Submission\Sections\Stub\AbstractSectionStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var  \Zend\ServiceManager\ServiceLocatorInterface | m\MockInterface */
    private $mockSl;
    /** @var  \Zend\ServiceManager\ServiceManager | m\MockInterface */
    private $mockSm;

    public function setUp()
    {
        $this->mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceManager::class)
            ->shouldReceive('getServiceLocator')->andReturn($this->mockSl)
            ->getMock();
    }

    public function testCreateService()
    {
        $name = 'unit_Name';
        $reqName = AbstractSectionStub::class;

        $this->mockSl
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        'viewrenderer' => m::mock(\Zend\View\Renderer\PhpRenderer::class),
                        'QueryHandlerManager' => m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class),
                    ];

                    return $map[$class];
                }
            );

        $actual = (new AbstractFactory())->createService($this->mockSm, $name, $reqName);

        static::assertInstanceOf(AbstractSectionStub::class, $actual);
    }
}
