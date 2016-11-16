<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ProcessedDataInputFactory;

/**
 * Class ProcessedDataInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter
 */
class ProcessedDataInputFactoryTest extends TestCase
{
    /**
     * Tests create service
     */
    public function testCreateService()
    {
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('Rules\ProcessedData\BusRegNotFound')->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Rules\ProcessedData\VariationNumber')->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\NewAppAlreadyExists')
            ->once()
            ->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\RegisteredBusRoute')
            ->once()
            ->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\LocalAuthorityMissing')
            ->once()
            ->andReturn($mockValidator);

        $sut = new ProcessedDataInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(5, $service->getValidatorChain());
    }

    /**
     * Tests create service with disabled validators
     */
    public function testCreateServiceDisabledValidators()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'processed_data' => false
                ]
            ]
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new ProcessedDataInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(0, $service->getValidatorChain());
    }
}
