<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;
use Dvsa\Olcs\Api\Service\Nr\Filter\Vrm as VrmFilter;
use Dvsa\Olcs\Api\Service\Nr\Validator\Vrm as VrmValidator;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\IsExecuted;

/**
 * Class ComplianceEpisodeInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\InputFilter
 */
class ComplianceEpisodeInputFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockMappings = m::mock(SpecificationInterface::class);

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockMapFilter = m::mock(MapXmlFile::class);
        $mockMapFilter->shouldReceive('setMapping')->with($mockMappings);

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('ComplianceEpisodeXmlMapping')->andReturn($mockMappings);

        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockMapFilter);
        $mockSl->shouldReceive('get')->with(IsExecuted::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(VrmFilter::class)->andReturn($mockFilter);

        $mockSl->shouldReceive('get')->with(VrmValidator::class)->andReturn($mockValidator);

        $sut = new ComplianceEpisodeInputFactory();
        /** @var \Zend\InputFilter\Input $service */
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(3, $service->getFilterChain());
        $this->assertCount(1, $service->getValidatorChain());
    }
}
