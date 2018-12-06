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
        $xmlNs = 'https://webgate.ec.testa.eu/erru/1.0';

        $config = [
            'nr' => [
                'compliance_episode' => [
                    'xmlNs' => $xmlNs
                ],
                'max_schema_errors' => $maxSchemaErrors
            ],
            'xml_valid_message_exclude' => $xmlExclude
        ];

        $mockXsdValidator = m::mock('Zend\Validator\AbstractValidator');
        $mockXsdValidator->shouldReceive('setXsd')->once()->with($xmlNs);
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
     * tests for missing config keys
     *
     * @param $config
     * @param $exceptionName
     * @param $exceptionMessage
     *
     * @dataProvider createServiceErrorProvider
     */
    public function testCreateServiceMissingConfig($config, $exceptionName, $exceptionMessage)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);

        $sut = new XmlStructureInputFactory();
        $sut->createService($mockSl);
    }

    /**
     * Data provider for testCreateServiceMissingConfig
     *
     * @return array
     */
    public function createServiceErrorProvider()
    {
        return [
            [
                [
                    'nr' => [
                        'compliance_episode' => [
                            'xmlNs' => 'xml ns info'
                        ]
                    ],
                    'xml_valid_message_exclude' => ['strings']
                ],
                \RuntimeException::class,
                XmlStructureInputFactory::MAX_SCHEMA_MSG
            ],
            [
                [
                    'nr' => [
                        'compliance_episode' => [
                            'xmlNs' => 'xml ns info'
                        ],
                        'max_schema_errors' => 10
                    ]
                ],
                \RuntimeException::class,
                XmlStructureInputFactory::XML_VALID_EXCLUDE_MSG
            ],
            [
                [
                    'nr' => [
                        'max_schema_errors' => 10
                    ],
                    'xml_valid_message_exclude' => ['strings']
                ],
                \RuntimeException::class,
                XmlStructureInputFactory::XML_NS_MSG
            ],
        ];
    }
}
