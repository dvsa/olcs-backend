<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\System\RefData;
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

    public function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $mockLicence = m::mock();

        $this->continuationDetail = new ContinuationDetail();
        $this->continuationDetail->setLicence($mockLicence);

        $mockApplicationReviewService = m::mock();
        $mockApplicationReviewService->shouldReceive('getMarkupForLicence')->once()->with($mockLicence)
            ->andReturn('DECLARATIONS');
        $serviceManager->setService('Review\ApplicationUndertakings', $mockApplicationReviewService);

        $mockTranslator = m::mock()->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return $message . '_translated';
            }
        )->getMock();
        $serviceManager->setService('translator', $mockTranslator);

        $this->sut = new DeclarationReviewService();
        $this->sut->setServiceLocator($serviceManager);
    }

    public function testGetConfigFromDataNoSignature()
    {
        $this->continuationDetail->setSignatureType(new RefData(RefData::SIG_SIGNATURE_NOT_REQUIRED));

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'DECLARATIONS'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.unknown_translated',
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

        $this->assertEquals(
            [
                'mainItems' => [
                    ['markup' => 'DECLARATIONS'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.print_translated',
                            ]
                        ]
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
                    ['markup' => 'DECLARATIONS'],
                    [
                        'header' => 'continuations.declaration.signature-details',
                        'items' => [
                            [
                                'label' => 'continuations.declaration.signature-type.label',
                                'value' => 'continuations.declaration.signature-type.digital_translated',
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
