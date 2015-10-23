<?php


namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\XmlStructureInputFactory;
use Olcs\XmlTools\Filter\ParseXml;
use Olcs\XmlTools\Validator\Xsd;

/**
 * Class XmlStructureInputFactoryTest
 * @package OlcsTest\Ebsr\src\InputFilter
 */
class XmlStructureInputFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockXsdValidator = m::mock('Zend\Validator\AbstractValidator');
        $mockXsdValidator->shouldReceive('setXsd')->once()
            ->with('http://www.transxchange.org.uk/schema/2.1/TransXChange_registration.xsd');

        $mockFilter = m::mock('Zend\Filter\AbstractFilter');
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
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
}
