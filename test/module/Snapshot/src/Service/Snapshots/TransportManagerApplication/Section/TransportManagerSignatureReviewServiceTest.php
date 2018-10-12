<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerSignatureReviewService;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * TransportManagerSignatureReviewServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerSignatureReviewServiceTest extends MockeryTestCase
{
    /** @var TransportManagerSignatureReviewService */
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerSignatureReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider getConfigProvider
     */
    public function testGetConfig($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        if (!$data['digitalSignature']) {
            $mockTranslator
                ->shouldReceive('translate')
                ->with(TransportManagerSignatureReviewService::SIGNATURE, 'snapshot')
                ->once()
                ->andReturn('%s_%s');
        }

        $mockTranslator
            ->shouldReceive('translate')
            ->with(TransportManagerSignatureReviewService::ADDRESS, 'snapshot')
            ->once()
            ->andReturn('ADDRESS');

        $mockTranslator->shouldReceive('translate')->with($expected['label'], 'snapshot')->once()
            ->andReturn($expected['label'] . 'translated');

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getTmDigitalSignature')->andReturn($data['digitalSignature']);

        $tma->shouldReceive('getOpDigitalSignature')->once()->andReturn(null);
        $tma->shouldReceive('getIsOwner')->once()->andReturn('N');

        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn($data['organisationType']);

        $expectedMarkup = $expected['label'] . 'translated_ADDRESS';

        if ($data['digitalSignature']) {
            $digitalSignatureDate = (new DateTime('2018-01-01'))->format('d-m-Y');
            $birthDate = (new DateTime('1980-01-01'))->format('d-m-Y');
            $signatureName = 'Name';
            $data['digitalSignature']
                ->shouldReceive('getCreatedOn')
                ->with()
                ->andReturn($digitalSignatureDate)
                ->shouldReceive('getDateOfBirth')
                ->with()
                ->andReturn($birthDate)
                ->shouldReceive('getSignatureName')
                ->with()
                ->andReturn($signatureName);

            $mockTranslator
                ->shouldReceive('translate')
                ->with(TransportManagerSignatureReviewService::SIGNATURE_DIGITAL, 'snapshot')
                ->once()
                ->andReturn('%s_%s_%s_%s_%s');

            $expectedMarkup = $signatureName .
                '_' . $birthDate .
                '_' . $digitalSignatureDate .
                '_' . $expectedMarkup;
        }

        $this->assertEquals(['markup' => $expectedMarkup], $this->sut->getConfig($tma));
    }

    public function getConfigProvider()
    {

        $digitalSignature = m::mock(DigitalSignature::class);

        return [
            'case_01' => [
                [
                    'organisationType' => 'unknown',
                    'digitalSignature' => null
                ],
                [
                    'label' => 'responsible-person-signature',
                ]
            ],
            'case_02' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_LLP,
                    'digitalSignature' => null
                ],
                [
                    'label' => 'directors-signature',
                ]
            ],
            'case_03' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_REGISTERED_COMPANY,
                    'digitalSignature' => null
                ],
                [
                    'label' => 'directors-signature',
                ]
            ],
            'case_04' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_PARTNERSHIP,
                    'digitalSignature' => null
                ],
                [
                    'label' => 'partners-signature',
                ]
            ],
            'case_05' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_SOLE_TRADER,
                    'digitalSignature' => null
                ],
                [
                    'label' => 'owners-signature',
                ]
            ],
            'case_06' => [
                [
                    'organisationType' => 'unknown',
                    'digitalSignature' => $digitalSignature
                ],
                [
                    'label' => 'responsible-person-signature',
                ]
            ],
        ];
    }

    /**
     * testAppropriateTemplateUsedForDigitalSignatures
     *
     * @dataProvider digitalSignatureDataProvider
     *
     * @param $conditions
     * @param $expected
     */
    public function testAppropriateTemplateUsedForDigitalSignatures($conditions, $expected)
    {

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator
            ->shouldReceive('translate')
            ->with(TransportManagerSignatureReviewService::ADDRESS, 'snapshot')
            ->once()
            ->andReturn('ADDRESS');

        $mockTranslator->shouldReceive('translate')->with($expected['label'], 'snapshot')->once()
            ->andReturn($expected['label'] . 'translated');

        $tma = m::mock(TransportManagerApplication::class);

        $tma->shouldReceive('getOpDigitalSignature')->once()->andReturn($conditions['OpSignature']);
        $tma->shouldReceive('getIsOwner')->once()->andReturn($conditions['isOwner']);
        $tma->shouldReceive('getTmDigitalSignature')->once()->andReturn($conditions['tmSignature']);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn(Organisation::ORG_TYPE_SOLE_TRADER);

        $mockTranslator
            ->shouldReceive('translate')
            ->with($conditions['markup'], 'snapshot')
            ->once()
            ->andReturn('%s_%s_%s_%s_%s');

        $actual = $this->sut->getConfig($tma);
        $this->assertEquals([
            'markup' => $expected['markup']
        ], $actual);
    }

    public function digitalSignatureDataProvider()
    {
        return [

                "as operator" => [
                    ['OpSignature' => true, 'isOwner' => true, 'tmSignature' => false, "markup" =>TransportManagerSignatureReviewService::SIGNATURE_DIGITAL],
                    ['label' => 'owners-signature', 'markup' => 'test']
                ]

        ];
    }
}
