<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ShortNoticeInputFactory;

/**
 * Class ShortNoticeInputFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\InputFilter
 */
class ShortNoticeInputFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockValidator = m::mock('Zend\Validator\AbstractValidator');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ValidatorManager')->andReturnSelf();

        $mockSl->shouldReceive('get')->with('Rules\ShortNotice\MissingSection')->once()->andReturn($mockValidator);
        $mockSl->shouldReceive('get')->with('Rules\ShortNotice\MissingReason')->once()->andReturn($mockValidator);

        $sut = new ShortNoticeInputFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Zend\InputFilter\Input', $service);
        $this->assertCount(2, $service->getValidatorChain());
    }
}
