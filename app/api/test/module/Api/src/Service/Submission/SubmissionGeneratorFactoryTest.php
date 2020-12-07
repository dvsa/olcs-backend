<?php

namespace Dvsa\OlcsTest\Api\Service\Submission;

use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory
 */
class SubmissionGeneratorFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        /** @var  \Laminas\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)
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

        $actual = (new SubmissionGeneratorFactory())->createService($mockSl);

        static::assertInstanceOf(SubmissionGenerator::class, $actual);
    }
}
