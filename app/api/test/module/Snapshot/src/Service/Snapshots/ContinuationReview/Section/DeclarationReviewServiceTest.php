<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\DeclarationReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use OlcsTest\Bootstrap;

/**
 * DeclarationReviewServiceTest
 */
class DeclarationReviewServiceTest extends MockeryTestCase
{
    /** @var DeclarationReviewService */
    protected $sut;

    /** @var ContinuationDetail */
    private $continuationDetail;

    public function setUp(): void
    {
        $serviceManager = Bootstrap::getServiceManager();

        /** @var var Organisation $organisation */
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_REGISTERED_COMPANY));
        /** @var Licence $mockLicence */
        $mockLicence = new Licence($organisation, new RefData(Licence::LICENCE_STATUS_VALID));
        $mockLicence->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_PSV));
        $mockLicence->setLicenceType(new RefData(Licence::LICENCE_TYPE_RESTRICTED));

        $this->continuationDetail = new ContinuationDetail();
        $this->continuationDetail->setLicence($mockLicence);

        $mockTranslator = m::mock()->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return $message . '_translated(%s)';
            }
        )->getMock();
        $serviceManager->setService('translator', $mockTranslator);

        $this->sut = new DeclarationReviewService();
        $this->sut->setServiceLocator($serviceManager);
    }

    public function testGetConfigFromDataNullSignature()
    {
        $this->continuationDetail->setSignatureType(null);

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'markup-continuation-declaration-psv_translated()'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.unknown_translated(%s)',
                            ]
                        ]
                    ]
                ]
            ],
            $this->sut->getConfigFromData($this->continuationDetail)
        );
    }

    /**
     * @dataProvider testGetConfigFromDataDeclarationMarkupDataProvider
     */
    public function testGetConfigFromDataDeclarationMarkup($expectedMarkup, $goodsOrPsv, $licenceType, $isNi)
    {
        $this->continuationDetail->setSignatureType(null);
        $this->continuationDetail->getLicence()->setGoodsOrPsv(new RefData($goodsOrPsv));
        $this->continuationDetail->getLicence()->setLicenceType(new RefData($licenceType));
        if ($isNi) {
            $this->continuationDetail->getLicence()->setTrafficArea((new TrafficArea())->setIsNi(true));
        }

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => $expectedMarkup],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.unknown_translated(%s)',
                            ]
                        ]
                    ]
                ]
            ],
            $this->sut->getConfigFromData($this->continuationDetail)
        );
    }

    public function testGetConfigFromDataDeclarationMarkupDataProvider()
    {
        return [
            [
                'markup-continuation-declaration-goods-gb_translated()',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_RESTRICTED,
                false,
            ],
            [
                'markup-continuation-declaration-goods-ni_translated()',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_RESTRICTED,
                true,
            ],
            [
                'markup-continuation-declaration-goods-gb_translated()',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                false,
            ],
            [
                'markup-continuation-declaration-goods-ni_translated()',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true,
            ],
            [
                'markup-continuation-declaration-goods-gb_translated'
                    .'(markup-continuation-declaration-goods-gb-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                false,
            ],
            [
                'markup-continuation-declaration-goods-ni_translated'
                    .'(markup-continuation-declaration-goods-ni-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                true,
            ],
            [
                'markup-continuation-declaration-goods-gb_translated'
                    .'(markup-continuation-declaration-goods-gb-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false,
            ],
            [
                'markup-continuation-declaration-goods-ni_translated'
                    .'(markup-continuation-declaration-goods-ni-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true,
            ],
            [
                'markup-continuation-declaration-psv_translated()',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_RESTRICTED,
                false,
            ],
            [
                'markup-continuation-declaration-psv_translated()',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_RESTRICTED,
                true,
            ],
            [
                'markup-continuation-declaration-psv-sr_translated()',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                false,
            ],
            [
                'markup-continuation-declaration-psv-sr_translated()',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true,
            ],
            [
                'markup-continuation-declaration-psv_translated'
                    .'(markup-continuation-declaration-psv-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                false,
            ],
            [
                'markup-continuation-declaration-psv_translated'
                    .'(markup-continuation-declaration-psv-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                true,
            ],
            [
                'markup-continuation-declaration-psv_translated'
                    .'(markup-continuation-declaration-psv-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false,
            ],
            [
                'markup-continuation-declaration-psv_translated'
                    .'(markup-continuation-declaration-psv-standard_translated(%s))',
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true,
            ],
        ];
    }

    public function testGetConfigFromDataNoSignature()
    {
        $this->continuationDetail->setSignatureType(new RefData(RefData::SIG_SIGNATURE_NOT_REQUIRED));

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'markup-continuation-declaration-psv_translated()'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.unknown_translated(%s)',
                            ]
                        ]
                    ]
                ]
            ],
            $this->sut->getConfigFromData($this->continuationDetail)
        );
    }

    public function testGetConfigFromDataPhysicalSignature()
    {
        $this->continuationDetail->setSignatureType(new RefData(RefData::SIG_PHYSICAL_SIGNATURE));
        $this->continuationDetail->getLicence()->setTrafficArea((new TrafficArea())->setIsNi(false));

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'markup-continuation-declaration-psv_translated()'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.print_translated(%s)',
                            ]
                        ]
                    ],
                    [
                        'markup' =>
                            'markup-continuation_signature_translated(undertakings_directors_signature_translated(%s))',
                    ]
                ]
            ],
            $this->sut->getConfigFromData($this->continuationDetail)
        );
    }

    public function testGetConfigFromDataDigitalSignature()
    {
        $this->continuationDetail->setSignatureType(new RefData(RefData::SIG_DIGITAL_SIGNATURE));
        $mockDigitalSignature = m::mock();
        $mockDigitalSignature->shouldReceive('getSignatureName')->with()->once()->andReturn('NAME');
        $mockDigitalSignature->shouldReceive('getDateOfBirth')->with()->once()->andReturn('2017-08-01');
        $mockDigitalSignature->shouldReceive('getCreatedOn')->with()->once()->andReturn('1900-01-01');

        $this->continuationDetail->setDigitalSignature($mockDigitalSignature);

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'markup-continuation-declaration-psv_translated()'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.digital_translated(%s)',
                            ],
                            [
                                'label' => 'continuations.declaration.signed-by',
                                'value' => 'NAME',
                            ],
                            [
                                'label' => 'continuations.declaration.date-of-birth',
                                'value' => '01 Aug 2017',
                            ],
                            [
                                'label' => 'continuations.declaration.signature-date',
                                'value' => '01 Jan 1900',
                            ],
                        ]
                    ]
                ]
            ],
            $this->sut->getConfigFromData($this->continuationDetail)
        );
    }
}
