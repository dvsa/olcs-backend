<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\DateFrom;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Date From test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateFromTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new DateFrom();
        $query = $bookmark->getQuery(['communityLic' => 123, 'application' => 456]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[0]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query[1]);
    }

    public function testRender()
    {
        $bookmark = new DateFrom();
        $bookmark->setData(
            [
                [
                    'specifiedDate' => new \DateTime('2014-02-03 11:12:34')
                ],
                [
                    'Count' => 0, 'Results' => []
                ]
            ]
        );

        $this->assertEquals(
            '03/02/2014',
            $bookmark->render()
        );
    }

    public function testRenderWithInterim()
    {
        $bookmark = new DateFrom();
        $bookmark->setData(
            [
                [
                    'specifiedDate' => new \DateTime('2014-02-03 11:12:34')
                ],
                [
                    'interimStatus' => [
                        'id' => Application::INTERIM_STATUS_INFORCE
                    ],
                    'interimStart' => new \DateTime('2011-01-01 10:10:10')
                ]
            ]
        );

        $this->assertEquals(
            '01/01/2011',
            $bookmark->render()
        );
    }
}
