<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\BusRegistrationInputFactory;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;

/**
 * Class BusRegistrationInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter
 */
class BusRegistrationInputFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockMappings = m::mock(SpecificationInterface::class);

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');
        $mockBreakValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockMapFilter = m::mock(MapXmlFile::class);
        $mockMapFilter->shouldReceive('setMapping')->with($mockMappings);

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('TransExchangeXmlMapping')->andReturn($mockMappings);

        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockMapFilter);
        $mockSl->shouldReceive('get')->with('InjectIsTxcApp')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('InjectReceivedDate')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('InjectNaptanCodes')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('IsScottishRules')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('Format\Subsidy')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('Format\Via')->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with('Format\ExistingRegNo')->andReturn($mockFilter);

        $mockSl->shouldReceive('get')->with('Rules\EffectiveDate')->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Rules\ApplicationType')->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Rules\Licence')->andReturn($mockValidator);

        $sut = new BusRegistrationInputFactory();
        /** @var \Zend\InputFilter\Input $service */
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(8, $service->getFilterChain());
        $this->assertCount(3, $service->getValidatorChain());

        foreach ($service->getValidatorChain()->getValidators() as $validator) {
            if ($validator['instance'] === $mockBreakValidator) {
                $this->assertTrue($validator['breakChainOnFailure'], 'Break chain on failure set incorrectly');
            } else {
                $this->assertFalse($validator['breakChainOnFailure'], 'Break chain on failure set incorrectly');
            }
        }
    }
}
