<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\BrRegVarOrCanc;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;

/**
 * BrRegVarOrCanc test
 */
class BrRegVarOrCancTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new BrRegVarOrCanc();

        $this->assertInstanceOf(
            \Dvsa\Olcs\Transfer\Query\QueryInterface::class,
            $bookmark->getQuery(['busRegId' => 123])
        );
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new BrRegVarOrCanc();
        $bookmark->setData($data);

        if ($expected === false) {
            $this->setExpectedException(
                \Exception::class,
                'Failed to generate bookmark Dvsa\Olcs\Api\Service\Document\Bookmark\BrRegVarOrCanc'
            );
        }
        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                [],
                false
            ],
            [
                [
                    'status' => ''
                ],
                false
            ],
            [
                [
                    'status' => ['id' => 'foo'],
                ],
                false
            ],
            [
                [
                    'status' => ['id' => BusReg::STATUS_NEW],
                ],
                'commence'
            ],
            [
                [
                    'status' => ['id' => BusReg::STATUS_CANCEL],
                ],
                'cancel'
            ],
            [
                [
                    'status' => ['id' => BusReg::STATUS_VAR],
                ],
                'vary'
            ],
            [
                [
                    'status' => ['id' => BusReg::STATUS_EXPIRED],
                ],
                false
            ],
        ];
    }
}
