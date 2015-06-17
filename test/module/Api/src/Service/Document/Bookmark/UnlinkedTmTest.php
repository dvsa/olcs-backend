<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\UnlinkedTm;

/**
 * Licence holder name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UnlinkedTmTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new UnlinkedTm();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderValidDataProvider()
    {
        return array(
            array(
                "Testy McTest",
                array(
                    'tmLicences' => array(
                        0 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'person' => array(
                                        'forename' => 'Testy',
                                        'familyName' => 'McTest'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            array(
                "Lorem Ipsum\nTesty McTest",
                array(
                    'tmLicences' => array(
                        0 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'person' => array(
                                        'forename' => 'Lorem',
                                        'familyName' => 'Ipsum'
                                    )
                                )
                            )
                        ),
                        1 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'person' => array(
                                        'forename' => 'Testy',
                                        'familyName' => 'McTest'
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            array(
                'To be nominated.',
                array(
                    'tmLicences' => array()
                )
            )
        );
    }

    /**
     * @dataProvider testRenderValidDataProvider
     */
    public function testRender($expected, $results)
    {
        $bookmark = new UnlinkedTm();
        $bookmark->setData($results);

        $this->assertEquals($expected, $bookmark->render());
    }
}
