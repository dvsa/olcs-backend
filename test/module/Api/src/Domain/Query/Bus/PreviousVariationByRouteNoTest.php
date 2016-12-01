<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Bus;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Bus\PreviousVariationByRouteNo;

/**
 * PreviousVariationByRouteNo test
 */
class PreviousVariationByRouteNoTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $variationNo = 1;
        $routeNo = 2;
        $licenceId = 3;

        $query = PreviousVariationByRouteNo::create(
            [
                'variationNo' => $variationNo,
                'routeNo' => $routeNo,
                'licenceId' => $licenceId
            ]
        );

        $this->assertSame($routeNo, $query->getRouteNo());
        $this->assertSame($variationNo, $query->getVariationNo());
        $this->assertSame($licenceId, $query->getLicenceId());
    }
}
