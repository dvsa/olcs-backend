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

    public function setUp()
    {
        $this->mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
    }

    public function testCreateService()
    {
        $options = ['option1', 'option2'];
        $reqName = 'unit_ReqClass';

        $cfg = [
            'submissions' => [
                'sections' => [
                    'aliases' => [
                        $reqName => AbstractSectionStub::class,
                    ],
                ],
            ],
        ];

        $this->mockSl
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) use ($cfg) {
                    $map = [
                        'Config' => $cfg,
                        'viewrenderer' => m::mock(\Zend\View\Renderer\PhpRenderer::class),
                        'QueryHandlerManager' => m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class),
                    ];

                    return $map[$class];
                }
            );

        $actual = (new AbstractFactory())->createService($this->mockSl, $reqName, $options);

        static::assertInstanceOf(
            AbstractSectionStub::class, $actual
        );
    }
}
