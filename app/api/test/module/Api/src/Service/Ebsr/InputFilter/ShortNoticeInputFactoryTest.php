<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ShortNoticeInputFactory;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason;

/**
 * Class ShortNoticeInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter
 */
class ShortNoticeInputFactoryTest extends TestCase
{
    /**
     * Tests create service
     */
    public function testCreateService()
    {
        $mockValidator = m::mock('Laminas\Validator\AbstractValidator');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with(MissingSection::class)->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with(MissingReason::class)->once()->andReturn($mockValidator);

        $sut = new ShortNoticeInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Laminas\InputFilter\Input', $service);
        $this->assertCount(2, $service->getValidatorChain());
    }

    /**
     * Tests create service with disabled validators
     */
    public function testCreateServiceDisabledValidators()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'short_notice' => false
                ]
            ]
        ];

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ShortNoticeInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Laminas\InputFilter\Input', $service);
        $this->assertCount(0, $service->getValidatorChain());
    }
}
