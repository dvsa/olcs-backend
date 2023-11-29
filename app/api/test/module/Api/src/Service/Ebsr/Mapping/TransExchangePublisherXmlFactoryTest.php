<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Mapping;

use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\Mapping\TransExchangePublisherXmlFactory;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;

/**
 * Class TransExchangePublisherXmlFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Mapping
 */
class TransExchangePublisherXmlFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $mockSl = m::mock(ContainerInterface::class);

        $sut = new TransExchangePublisherXmlFactory();

        $service = $sut->__invoke($mockSl, null);

        $this->assertInstanceOf(SpecificationInterface::class, $service);
    }
}
