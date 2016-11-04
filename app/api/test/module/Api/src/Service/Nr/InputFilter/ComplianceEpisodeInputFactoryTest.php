<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\InputFilter;

use Dvsa\Olcs\Api\Service\Nr\Filter;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;

/**
 * Class ComplianceEpisodeInputFactoryTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @covers \Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory
 */
class ComplianceEpisodeInputFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockMappings = m::mock(SpecificationInterface::class);

        $mockFilter = m::mock(\Zend\Filter\AbstractFilter::class);

        $mockMapFilter = m::mock(MapXmlFile::class);
        $mockMapFilter->shouldReceive('setMapping')->with($mockMappings);

        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('ComplianceEpisodeXmlMapping')->andReturn($mockMappings);

        $mockSl->shouldReceive('get')->with(MapXmlFile::class)->andReturn($mockMapFilter);
        $mockSl->shouldReceive('get')->with(Filter\Vrm::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(Filter\LicenceNumber::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(Filter\MemberStateCode::class)->andReturn($mockFilter);

        $sut = new ComplianceEpisodeInputFactory();
        /** @var \Zend\InputFilter\Input $service */
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(4, $service->getFilterChain());
    }
}
