<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\AuthorisedVehicles;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use PHPUnit\Framework\TestCase;

class AuthorisedVehiclesTest extends TestCase
{
    public function testGetQuery()
    {
        $bookmark = new AuthorisedVehicles();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(QueryInterface::class, $query);
        $this->assertEquals(123, $query->getId());
    }

    /**
     * @dataProvider dpRender
     */
    public function testRender($data, $expected)
    {
        $bookmark = new AuthorisedVehicles();
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
                    'totAuthVehicles' => 10,
                    'totAuthHgvVehicles' => 10,
                    'totAuthLgvVehicles' => null,
                ],
                'expected' => 10,
            ],
            'hgvOnly' => [
                'data' => [
                    'totAuthVehicles' => 10,
                    'totAuthHgvVehicles' => 10,
                    'totAuthLgvVehicles' => 0,
                ],
                'expected' => 10,
            ],
            'lgvOnly' => [
                'data' => [
                    'totAuthVehicles' => 10,
                    'totAuthHgvVehicles' => 0,
                    'totAuthLgvVehicles' => 10,
                ],
                'expected' => '10 Light goods vehicles',
            ],
            'lgvOnlyOne' => [
                'data' => [
                    'totAuthVehicles' => 1,
                    'totAuthHgvVehicles' => 0,
                    'totAuthLgvVehicles' => 1,
                ],
                'expected' => '1 Light goods vehicles',
            ],
            'hgvAndLgv' => [
                'data' => [
                    'totAuthVehicles' => 10,
                    'totAuthHgvVehicles' => 7,
                    'totAuthLgvVehicles' => 3,
                ],
                'expected' => "7 Heavy goods vehicles\n\n3 Light goods vehicles",
            ],
            'hgvAndLgvOneEach' => [
                'data' => [
                    'totAuthVehicles' => 2,
                    'totAuthHgvVehicles' => 1,
                    'totAuthLgvVehicles' => 1,
                ],
                'expected' => "1 Heavy goods vehicles\n\n1 Light goods vehicles",
            ],
        ];
    }
}
