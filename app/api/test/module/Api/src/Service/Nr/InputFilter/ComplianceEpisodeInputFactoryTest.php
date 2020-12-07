<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\InputFilter;

use Dvsa\Olcs\Api\Service\Nr\Filter;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class ComplianceEpisodeInputFactoryTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @covers \Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory
 */
class ComplianceEpisodeInputFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockVrmFilter = m::mock(Filter\Vrm::class);
        $mockLicenceNumberFilter = m::mock(Filter\LicenceNumber::class);
        $mockMemberStateFilter = m::mock(Filter\Format\MemberStateCode::class);

        $mockSl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with(Filter\Vrm::class)->andReturn($mockVrmFilter);
        $mockSl->shouldReceive('get')->with(Filter\LicenceNumber::class)->andReturn($mockLicenceNumberFilter);
        $mockSl->shouldReceive('get')->with(Filter\Format\MemberStateCode::class)->andReturn($mockMemberStateFilter);

        $sut = new ComplianceEpisodeInputFactory();
        /** @var \Laminas\InputFilter\Input $service */
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Laminas\InputFilter\Input', $service);
        $this->assertCount(3, $service->getFilterChain());
    }
}
