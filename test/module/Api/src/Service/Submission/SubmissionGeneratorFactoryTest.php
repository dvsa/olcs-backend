<?php

namespace Dvsa\OlcsTest\Api\Service\Submission;

use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory
 */
class SubmissionGeneratorFactoryTest extends MockeryTestCase
{
    public function testInvoke()
    {
        /** @var  \Laminas\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        SectionGeneratorPluginManager::class => m::mock(SectionGeneratorPluginManager::class),
                        'Config' => [
                            'submissions' => [],
                        ]
                    ];

                    return $map[$class];
                }
            )
            ->getMock();

        $actual = (new SubmissionGeneratorFactory())->__invoke($mockSl, SubmissionGenerator::class);

        static::assertInstanceOf(SubmissionGenerator::class, $actual);
    }
}
