<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\InterimUnlinkedTm;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Interim Unlinked TM test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimUnlinkedTmTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new InterimUnlinkedTm();
        $query = $bookmark->getQuery(['application' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithRestrictedLicence()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => Licence::LICENCE_TYPE_RESTRICTED
                ]
            ]
        );

        $this->assertEquals('N/A', $bookmark->render());
    }

    public function testRenderWithNoTms()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                'transportManagers' => []
            ]
        );

        $this->assertEquals('None added as part of this application', $bookmark->render());
    }

    public function testRenderWithTms()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                'transportManagers' => [
                    [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'forename' => 'A',
                                    'familyName' => 'Person'
                                ]
                            ]
                        ]
                    ], [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'forename' => 'B',
                                    'familyName' => 'Person'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals("A Person\nB Person", $bookmark->render());
    }
}
