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
    public function testCreateService()
    {
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('Rules\ProcessedData\BusRegNotFound')->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\NewAppAlreadyExists')
            ->once()
            ->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\RegisteredBusRoute')
            ->once()
            ->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\LocalAuthorityNotRequired')
            ->once()
            ->andReturn($mockValidator);
        $mockSl->shouldReceive('get')
            ->with('Rules\ProcessedData\LocalAuthorityMissing')
            ->once()
            ->andReturn($mockValidator);

        $sut = new ProcessedDataInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(2, $service->getValidatorChain());
    }
}
