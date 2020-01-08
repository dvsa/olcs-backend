<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\CompanyStatus;
use PHPUnit\Framework\TestCase;

class CompanyStatusTest extends TestCase
{
    public function testGetQuery()
    {
        $bookmark = new CompanyStatus();
        $query = $bookmark->getQuery(['licence' => 123, 'bundle' => []]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider dptestRender
     */
    public function testRender($companyStatus)
    {
        $bookmark = new CompanyStatus();
        $bookmark->setData(
            ['companyStatus' => $companyStatus['status']]
        );

        $this->assertEquals(
            $companyStatus['expected'],
            $bookmark->render()
        );
    }

    public function dptestRender()
    {
        return [
            [
                [
                    'status' => 'liquidation',
                    'expected' => 'liquidation'
                ],
                [
                    'status' => null,
                    'expected' => null
                ]
            ]
        ];
    }
}
