<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\InputFilter;

use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Nr\InputFilter\XmlStructureInputFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;
use Psr\Container\ContainerInterface;

class XmlStructureInputFactoryTest extends TestCase
{
    /**
     * Tests create service
     */
    public function testInvoke()
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

        $mockXsdValidator = m::mock(\Laminas\Validator\AbstractValidator::class);
        $mockXsdValidator->shouldReceive('setXsd')->once()->with($xmlNs);
        $mockXsdValidator->shouldReceive('setMaxErrors')->once()->with($maxSchemaErrors);
        $mockXsdValidator->shouldReceive('setXmlMessageExclude')->once()->with($xmlExclude);

        $mockFilter = m::mock(\Laminas\Filter\AbstractFilter::class);

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('config')->once()->andReturn($config);
        $mockSl->shouldReceive('get')->with(ParseXmlString::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);

        $sut = new XmlStructureInputFactory();
        $service = $sut->__invoke($mockSl, Input::class);

        $this->assertInstanceOf(Input::class, $service);
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
     * @dataProvider invokeErrorProvider
     */
    public function testInvokeMissingConfig($config, $exceptionName, $exceptionMessage)
    {
        $this->expectException($exceptionName);
        $this->expectExceptionMessage($exceptionMessage);
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->once()->andReturn($config);

        $sut = new XmlStructureInputFactory();
        $sut->__invoke($mockSl, Input::class);
    }

    /**
     * Data provider for testInvokeMissingConfig
     */
    public function invokeErrorProvider(): array
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
