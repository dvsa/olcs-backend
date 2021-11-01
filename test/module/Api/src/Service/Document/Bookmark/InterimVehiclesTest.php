<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\InterimVehicles;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use PHPUnit\Framework\TestCase;

class InterimVehiclesTest extends TestCase
{
    public function testGetQuery()
    {
        $bookmark = new InterimVehicles();
        $query = $bookmark->getQuery(['application' => 123]);

        $this->assertInstanceOf(QueryInterface::class, $query);
        $this->assertEquals(123, $query->getId());
    }

    /**
     * @dataProvider dpRender
     */
    public function testRender($data, $expected)
    {
        $bookmark = new InterimVehicles();
        $bookmark->setData($data);

        $this->assertEquals(
            $expected,
            $bookmark->render()
        );
    }

    public function dpRender()
    {
        return [
            'notEligibleForLgv' => [
                'data' => [
                    'interimAuthVehicles' => 10,
                    'interimAuthHgvVehicles' => 10,
                    'interimAuthLgvVehicles' => null,
                ],
                'expected' => 10,
            ],
            'hgvOnly' => [
                'data' => [
                    'interimAuthVehicles' => 10,
                    'interimAuthHgvVehicles' => 10,
                    'interimAuthLgvVehicles' => 0,
                ],
                'expected' => 10,
            ],
            'lgvOnly' => [
                'data' => [
                    'interimAuthVehicles' => 10,
                    'interimAuthHgvVehicles' => 0,
                    'interimAuthLgvVehicles' => 10,
                ],
                'expected' => '10 Light goods vehicles',
            ],
            'lgvOnlyOne' => [
                'data' => [
                    'interimAuthVehicles' => 1,
                    'interimAuthHgvVehicles' => 0,
                    'interimAuthLgvVehicles' => 1,
                ],
                'expected' => '1 Light goods vehicles',
            ],
            'hgvAndLgv' => [
                'data' => [
                    'interimAuthVehicles' => 10,
                    'interimAuthHgvVehicles' => 7,
                    'interimAuthLgvVehicles' => 3,
                ],
                'expected' => "7 Heavy goods vehicles\n\n3 Light goods vehicles",
            ],
            'hgvAndLgvOneEach' => [
                'data' => [
                    'interimAuthVehicles' => 2,
                    'interimAuthHgvVehicles' => 1,
                    'interimAuthLgvVehicles' => 1,
                ],
                'expected' => "1 Heavy goods vehicles\n\n1 Light goods vehicles",
            ],
        ];
    }
}
