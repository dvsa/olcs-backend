<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Mapping;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Nr\Mapping\ComplianceEpisodeXmlFactory;

/**
 * Class ComplianceEpisodeXmlFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Mapping
 */
class ComplianceEpisodeXmlFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new ComplianceEpisodeXmlFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\XmlTools\Xml\Specification\SpecificationInterface', $service);
    }
}
