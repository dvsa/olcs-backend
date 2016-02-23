<?php

namespace Dvsa\OlcsTest\Api\Service\Nr\Filter;

use Dvsa\Olcs\Api\Service\Nr\Filter\Vrm;
use Dvsa\Olcs\Transfer\Filter\Vrm as TransferVrmFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class VrmTest
 * @package Dvsa\OlcsTest\Api\Service\Nr\Filter
 */
class VrmTest extends TestCase
{
    /**
     * test filter()
     */
    public function testFilter()
    {
        $initialValue = 'icZs';
        $expectedResult = '1CZS';
        $value = ['vrm' => $initialValue];
        $expected = ['vrm' => $expectedResult];

        $mockTransferFilter = m::mock(TransferVrmFilter::class);
        $mockTransferFilter->shouldReceive('filter')->with($initialValue)->andReturn($expectedResult);

        $sut = new Vrm();
        $sut->setVrmFilter($mockTransferFilter);

        $this->assertEquals($expected, $sut->filter($value));
    }
}
