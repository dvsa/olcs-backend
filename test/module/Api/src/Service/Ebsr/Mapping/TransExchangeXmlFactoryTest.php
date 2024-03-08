<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Mapping;

use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\Mapping\TransExchangeXmlFactory;
use Olcs\XmlTools\Xml\Specification\SpecificationInterface;

/**
 * Class TransExchangeXmlFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Mapping
 */
class TransExchangeXmlFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $mockSl = m::mock(ContainerInterface::class);

        $sut = new TransExchangeXmlFactory();

        $service = $sut->__invoke($mockSl, null);

        $this->assertInstanceOf(SpecificationInterface::class, $service);
    }
}
