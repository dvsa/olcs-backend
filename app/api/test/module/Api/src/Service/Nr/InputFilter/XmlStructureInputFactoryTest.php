<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\XmlStructureInputFactory;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;

/**
 * Class XmlStructureInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\InputFilter
 */
class XmlStructureInputFactoryTest extends TestCase
{
    /**
     * Tests create service
     */
    public function testCreateService()
    {
        $xmlExclude = ['strings'];
        $maxSchemaErrors = 10;

        $config = [
            'nr' => [
                'max_schema_errors' => $maxSchemaErrors
            ],
            'xml_valid_message_exclude' => $xmlExclude
        ];

        $mockXsdValidator = m::mock('Zend\Validator\AbstractValidator');
        $mockXsdValidator->shouldReceive('setXsd')->once()->with('https://webgate.ec.testa.eu/erru/1.0');
        $mockXsdValidator->shouldReceive('setMaxErrors')->once()->with($maxSchemaErrors);
        $mockXsdValidator->shouldReceive('setXmlMessageExclude')->once()->with($xmlExclude);

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);
        $mockSl->shouldReceive('get')->with(ParseXmlString::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);

        $sut = new XmlStructureInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(1, $service->getFilterChain());
        $this->assertCount(1, $service->getValidatorChain());
    }

    /**
     * Tests exception when max schema errors config is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No config specified for max_schema_errors
     */
    public function testCreateServiceMissingMaxSchema()
    {
        $config = [
            'nr' => [],
            'xml_valid_message_exclude' => ['strings']
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);

        $sut = new XmlStructureInputFactory();
        $sut->createService($mockSl);
    }

    /**
     * Tests exception when xml message exclude config is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No config specified for xml messages to exclude
     */
    public function testCreateServiceMissingXmlMessageExclude()
    {
        $config = [
            'nr' => [
                'max_schema_errors' => 10
            ]
        ];

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);

        $sut = new XmlStructureInputFactory();
        $sut->createService($mockSl);
    }
}
