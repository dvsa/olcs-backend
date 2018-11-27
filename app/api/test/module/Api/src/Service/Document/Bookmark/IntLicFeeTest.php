<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\IntLicFee;

/**
 * Interim Licence Fee bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IntLicFeeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new IntLicFee();
        $query = $bookmark->getQuery(['fee' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoIntLicFee()
    {
        $bookmark = new IntLicFee();
        $bookmark->setData(
            [
                'amount' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithIntLicFee()
    {
        $bookmark = new IntLicFee();
        $bookmark->setData(
            [
                'amount' => '123456'
            ]
        );

        $this->assertEquals(
            '123,456',
            $bookmark->render()
        );
    }
}
