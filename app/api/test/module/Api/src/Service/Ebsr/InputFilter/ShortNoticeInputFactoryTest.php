<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ShortNoticeInputFactory;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason;

class ShortNoticeInputFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $mockValidator = m::mock('Laminas\Validator\AbstractValidator');

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with(MissingSection::class)->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with(MissingReason::class)->once()->andReturn($mockValidator);

        $sut = new ShortNoticeInputFactory();
        $service = $sut->__invoke($mockSl, Input::class);

        $this->assertInstanceOf(Input::class, $service);
        $this->assertCount(2, $service->getValidatorChain());
    }

    /**
     * Tests create service with disabled validators
     */
    public function testInvokeDisabledValidators()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'short_notice' => false
                ]
            ]
        ];

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ShortNoticeInputFactory();
        $service = $sut->__invoke($mockSl, Input::class);

        $this->assertInstanceOf(Input::class, $service);
        $this->assertCount(0, $service->getValidatorChain());
    }
}
