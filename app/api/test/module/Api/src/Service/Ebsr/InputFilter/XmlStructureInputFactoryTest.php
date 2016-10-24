<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\XmlStructureInputFactory;
use Olcs\XmlTools\Filter\ParseXml;
use Olcs\XmlTools\Validator\Xsd;

/**
 * Class XmlStructureInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter
 */
class XmlStructureInputFactoryTest extends TestCase
{
    /**
     * Tests create service
     */
    public function testCreateService()
    {
        $maxSchemaErrors = 3;
        $schemaVersion = 2.5;

        $config = [
            'ebsr' => [
                'validate' => [
                    'xml_structure' => true
                ],
                'max_schema_errors' => $maxSchemaErrors,
                'transxchange_schema_version' => $schemaVersion
            ]
        ];

        $mockXsdValidator = m::mock('Zend\Validator\AbstractValidator');
        $mockXsdValidator->shouldReceive('setXsd')->once()
            ->with('http://www.transxchange.org.uk/schema/' . $schemaVersion . '/TransXChange_registration.xsd');
        $mockXsdValidator->shouldReceive('setMaxErrors')->once()->with($maxSchemaErrors);

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with(ParseXml::class)->andReturn($mockFilter);
        $mockSl->shouldReceive('get')->with(Xsd::class)->andReturn($mockXsdValidator);
        $mockSl->shouldReceive('get')->with('Structure\Operator')->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Structure\Registration')->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Structure\ServiceClassification')->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Structure\SupportingDocuments')->andReturn($mockValidator);

        $sut = new XmlStructureInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(1, $service->getFilterChain());
        $this->assertCount(5, $service->getValidatorChain());
    }

    /**
     * Tests create service with validation disabled
     */
    public function testCreateServiceDisabledValidators()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'xml_structure' => false
                ]
            ]
        ];

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('get')->with('FilterManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with(ParseXml::class)->andReturn($mockFilter);

        $sut = new XmlStructureInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(1, $service->getFilterChain());
        $this->assertCount(0, $service->getValidatorChain());
    }

    /**
     * test correct exception thrown when the max errors config is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No config specified for max_schema_errors
     */
    public function testCreateServiceMissingMaxErrorsConfig()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'xml_structure' => true
                ]
            ]
        ];

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);
        $mockSl->shouldReceive('get')->with('FilterManager')->once()->andReturnSelf();
        $mockSl->shouldReceive('get')->with(ParseXml::class)->once()->andReturn($mockFilter);

        $sut = new XmlStructureInputFactory();
        $sut->createService($mockSl);
    }

    /**
     * test correct exception thrown when the max errors config is missing
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No config specified for transxchange schema version
     */
    public function testCreateServiceMissingSchemaVersionConfig()
    {
        $config = [
            'ebsr' => [
                'validate' => [
                    'xml_structure' => true
                ],
                'max_schema_errors' => 3
            ]
        ];

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->once()->andReturn($config);
        $mockSl->shouldReceive('get')->with('FilterManager')->once()->andReturnSelf();
        $mockSl->shouldReceive('get')->with(ParseXml::class)->once()->andReturn($mockFilter);

        $sut = new XmlStructureInputFactory();
        $sut->createService($mockSl);
    }
}
