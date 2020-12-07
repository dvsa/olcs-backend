<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Mapping;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\Mapping\TransExchangePublisherXmlFactory;

/**
 * Class TransExchangePublisherXmlFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Mapping
 */
class TransExchangePublisherXmlFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');

        $sut = new TransExchangePublisherXmlFactory();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\XmlTools\Xml\Specification\SpecificationInterface', $service);
    }
}
