<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\SignatureReviewService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use OlcsTest\Bootstrap;

class SignatureReviewServiceTest extends MockeryTestCase
{
    /**
     * @var SignatureReviewService
     */
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new SignatureReviewService();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider physicalSignatureDataProvider
     */
    public function testPhysicalSignature($data, $expected)
    {
        $translator = $this->mockTranslator();

        $translator->shouldReceive('translate')
            ->with($expected['markup'])
            ->andReturn($expected['markup'] . '-translated');

        $translator->shouldReceive('translate')
            ->with($expected['signature_address'])
            ->andReturn($expected['signature_address'] . '-translated');

        $translator->shouldReceive('translate')
            ->with('markup-application_undertakings_signature')
            ->andReturn('markup-application_undertakings_signature-translated');


        $signatureType = m::mock(RefData::class);
        $signatureType->shouldReceive('getId')
            ->andReturn(RefData::SIG_PHYSICAL_SIGNATURE);

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getType->getId')->andReturn($data['org_type']);

        $markup = $this->sut->getConfigFromData([
            'signatureType' => $signatureType,
            'digitalSignature' => null,
            'organisation' => $organisation,
            'isNi' => $data['is_ni']
        ]);

        $expected = [
            'markup' => "markup-application_undertakings_signature-translated"
        ];

        $this->assertEquals($expected, $markup);
    }

    public function testDigitalSignature()
    {
        $signature = m::mock(DigitalSignature::class);
        $signature->shouldReceive('getSignatureName')->andReturn('test-name');
        $signature->shouldReceive('getDateOfBirth')->andReturn("2019-01-29");
        $signature->shouldReceive('getCreatedOn')->andReturn("2019-01-29");

        $translator = $this->mockTranslator();
        $translator->shouldReceive('translate')
            ->with('markup-signature-digital')
            ->andReturn('%s__%s__%s__translated');

        $signatureType = m::mock(RefData::class);
        $signatureType->shouldReceive('getId')
            ->andReturn(RefData::SIG_DIGITAL_SIGNATURE);


        $markup = $this->sut->getConfigFromData([
            'signatureType' => $signatureType,
            'digitalSignature' => $signature
        ]);
        $expected = ['markup' => "test-name__29 Jan 2019__29 Jan 2019__translated"];

        $this->assertEquals($expected, $markup);
    }

    protected function mockTranslator()
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);
        return $mockTranslator;
    }

    public function physicalSignatureDataProvider()
    {
        return [
            'is_not_ni' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_REGISTERED_COMPANY
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_directors_signature'
                ]
            ],
            'is_ni' => [
                'data' => [
                    'is_ni' => true,
                    'org_type' => Organisation::ORG_TYPE_REGISTERED_COMPANY
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_ni',
                    'markup' => 'undertakings_directors_signature'
                ]
            ],
            'registered_company' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_REGISTERED_COMPANY
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_directors_signature'
                ]
            ],
            'llp' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_LLP
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_directors_signature'
                ]
            ],
            'partnership' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_PARTNERSHIP
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_partners_signature'
                ]
            ],
            'sole_trader' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_SOLE_TRADER
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_owners_signature'
                ]
            ],
            'other' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_OTHER
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_responsiblepersons_signature'
                ]
            ],
            'irfo' => [
                'data' => [
                    'is_ni' => false,
                    'org_type' => Organisation::ORG_TYPE_IRFO
                ],
                'expected' => [
                    'signature_address' => 'markup-application_undertakings_signature_address_gb',
                    'markup' => 'undertakings_responsiblepersons_signature'
                ]
            ],
        ];
    }
}
