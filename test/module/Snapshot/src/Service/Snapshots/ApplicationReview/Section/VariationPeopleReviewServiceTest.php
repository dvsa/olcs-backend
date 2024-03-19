<?php

/**
 * Variation People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\AbstractReviewServiceServices;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\PeopleReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationPeopleReviewService;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Laminas\I18n\Translator\TranslatorInterface;

/**
 * Variation People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPeopleReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    /** @var TranslatorInterface */
    protected $mockTranslator;

    /** @var PeopleReviewService */
    protected $mockPeopleReview;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(TranslatorInterface::class);

        $abstractReviewServiceServices = m::mock(AbstractReviewServiceServices::class);
        $abstractReviewServiceServices->shouldReceive('getTranslator')
            ->withNoArgs()
            ->andReturn($this->mockTranslator);

        $this->mockPeopleReview = m::mock(PeopleReviewService::class);

        $this->sut = new VariationPeopleReviewService(
            $abstractReviewServiceServices,
            $this->mockPeopleReview
        );
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

        $this->mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                fn($string) => $string . '-translated'
            );

        $expected = ['freetext' => 'variation-review-people-change-translated'];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfigFromData($data, $noOfPeople, $expected)
    {
        $this->mockPeopleReview->shouldReceive('shouldShowPosition')
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
