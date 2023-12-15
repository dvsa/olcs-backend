<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\AandDStoredPublicationDate;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * AandDStoredPublicationDate bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AandDStoredPublicationDateTest extends MockeryTestCase
{
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new AandDStoredPublicationDate();
    }

    public function testGetQuery()
    {
        $query = $this->sut->getQuery(['application' => 1]);
        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider publicationsProvider
     */
    public function testRender($data, $result)
    {
        $this->sut->setData($data);
        $this->assertEquals($result, $this->sut->render());
    }

    public function publicationsProvider()
    {
        return [
            [
                [
                    'publicationLinks' => [
                        [
                            'publication' => [
                                'pubDate' => '2015-10-31',
                                'id' => 2,
                                'publicationNo' => '4'
                            ],
                            'publicationSection' => [
                                'id' => 1
                            ]
                        ],
                    ],
                ],
                '31/10/2015'
            ],
            [
                ['publicationLinks' => []],
                ''
            ],
            [
                [],
                ''
            ]
        ];
    }
}
