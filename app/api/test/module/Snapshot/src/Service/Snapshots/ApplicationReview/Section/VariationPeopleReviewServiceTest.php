<?php

/**
 * Variation People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationPeopleReviewService;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Variation People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPeopleReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sut = new VariationPeopleReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider simpleProvider
     */
    public function testGetConfigFromDataSimple($orgType)
    {
        $data = [
            'licence' => [
                'organisation' => [
                    'type' => [
                        'id' => $orgType
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $expected = ['freetext' => 'variation-review-people-change-translated'];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfigFromData($data, $noOfPeople, $expected)
    {
        $mockPeopleReview = m::mock();
        $this->sm->setService('Review\People', $mockPeopleReview);

        $mockPeopleReview->shouldReceive('shouldShowPosition')
            ->with($data)
            ->andReturn(true)
            ->shouldReceive('getConfigFromData')
            ->times($noOfPeople)
            ->andReturn('PERSON_CONFIG');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'applicationOrganisationPersons' => [
                        [
                            'action' => 'A',
                            'person' => 'Andy'
                        ],
                        [
                            'action' => 'U',
                            'person' => 'Uncle Sam'
                        ],
                        [
                            'action' => 'D',
                            'person' => 'Danny'
                        ]
                    ],
                    'licence' => [
                        'organisation' => [
                            'type' => [
                                'id' => Organisation::ORG_TYPE_REGISTERED_COMPANY
                            ]
                        ]
                    ]
                ],
                3,
                [
                    'subSections' => [
                        [
                            'title' => 'variation-review-people-A-title',
                            'mainItems' => [
                                'PERSON_CONFIG'
                            ]
                        ],
                        [
                            'title' => 'variation-review-people-U-title',
                            'mainItems' => [
                                'PERSON_CONFIG'
                            ]
                        ],
                        [
                            'title' => 'variation-review-people-D-title',
                            'mainItems' => [
                                'PERSON_CONFIG'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function simpleProvider()
    {
        return [
            [Organisation::ORG_TYPE_SOLE_TRADER],
            [Organisation::ORG_TYPE_PARTNERSHIP]
        ];
    }
}
