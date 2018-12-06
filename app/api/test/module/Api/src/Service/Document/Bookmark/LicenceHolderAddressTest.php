<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\LicenceHolderAddress;

/**
 * Licence holder address test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceHolderAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new LicenceHolderAddress();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoCorrespondenceAddress()
    {
        $bookmark = new LicenceHolderAddress();
        $bookmark->setData(
            [
                'correspondenceCd' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithCorrespondenceAddress()
    {
        $bookmark = new LicenceHolderAddress();
        $bookmark->setData(
            [
                'correspondenceCd' => [
                    'address' => [
                        'addressLine1' => 'Line 1'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'Line 1',
            $bookmark->render()
        );
    }
}
