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
    /** @var TransportMan
     * agerSignatureReviewService */
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


        $mockTranslator
            ->shouldReceive('translate')
            ->with(TransportManagerSignatureReviewService::ADDRESS, 'snapshot')
            ->once()
            ->andReturn('ADDRESS');

        $mockTranslator->shouldReceive('translate')->with($expected['label'], 'snapshot')->once()
            ->andReturn($expected['label'] . 'translated');

        if (empty($data['TmDigitalSignature']->getSignatureName())) {
            $mockTranslator->shouldReceive('translate')->with($expected['markup'], 'snapshot')->once()->
            andReturn(
                '%s_%s'
            );
        }


        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getTmDigitalSignature')->andReturn($data['TmDigitalSignature']);

        $tma->shouldReceive('getOpDigitalSignature')->twice()->andReturn($data['OpDigitalSignature']);
        $tma->shouldReceive('getIsOwner')->once()->andReturn('N');

        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn($data['organisationType']);

        $expectedMarkup = $expected['label'] . 'translated_ADDRESS';

        if ($data['TmDigitalSignature']->getSignatureName()) {
            $digitalSignatureDate = (new DateTime('2018-01-01'))->format('d-m-Y');
            $birthDate = (new DateTime('1980-01-01'))->format('d-m-Y');
            $signatureName = 'Name';
            $tma->shouldReceive('getOpDigitalSignature')->twice()->andReturn($data['OpDigitalSignature']);
            $data['OpDigitalSignature'] = m::mock(DigitalSignature::class);
            $data['OpDigitalSignature']
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
                ->with(TransportManagerSignatureReviewService::SIGNATURE_DIGITAL_BOTH, 'snapshot')
                ->once()
                ->andReturn('%s_%s_%s_%s_%s_%s_%s_%s');

            $expectedMarkup = $signatureName .
                '_' . $birthDate .
                '_' . $digitalSignatureDate .
                '_'. $signatureName .
                '_' . $birthDate .
                '_' . $digitalSignatureDate .
                '_' . $expectedMarkup;
        }

        $this->assertEquals(['markup' => $expectedMarkup], $this->sut->getConfig($tma));
    }

    public function getConfigProvider()
    {

        $emptyDigitalSignature = m::mock(DigitalSignature::class);
        $emptyDigitalSignature->shouldReceive('getSignatureName')->andReturn(false);

        $digitalSignature = m::mock(DigitalSignature::class);
        $digitalSignature->shouldReceive('getSignatureName')->andReturn('Name');
        $digitalSignature->shouldReceive('getDateOfBirth')->andReturn('01-01-1980');
        $digitalSignatureDate = (new DateTime('2018-01-01'))->format('d-m-Y');
        $digitalSignature->shouldReceive('getCreatedOn')->andReturn($digitalSignatureDate);

        return [
            'case_01' => [
                [
                    'organisationType' => 'unknown',
                    'TmDigitalSignature' => $emptyDigitalSignature,
                    'OpDigitalSignature' => $emptyDigitalSignature
                ],
                [
                    'label' => 'responsible-person-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE
                ]
            ],
            'case_02' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_LLP,
                    'TmDigitalSignature' => $emptyDigitalSignature,
                    'OpDigitalSignature' => $emptyDigitalSignature
                ],
                [
                    'label' => 'directors-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE
                ]
            ],
            'case_03' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_REGISTERED_COMPANY,
                    'TmDigitalSignature' => $emptyDigitalSignature,
                    'OpDigitalSignature' => $emptyDigitalSignature
                ],
                [
                    'label' => 'directors-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE
                ]
            ],
            'case_04' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_PARTNERSHIP,
                    'TmDigitalSignature' => $emptyDigitalSignature,
                    'OpDigitalSignature' => $emptyDigitalSignature
                ],
                [
                    'label' => 'partners-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE
                ]
            ],
            'case_05' => [
                [
                    'organisationType' => Organisation::ORG_TYPE_SOLE_TRADER,
                    'TmDigitalSignature' => $emptyDigitalSignature,
                    'OpDigitalSignature' => $emptyDigitalSignature
                ],
                [
                    'label' => 'owners-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE
                ]
            ],
            'case_06' => [
                [
                    'organisationType' => 'unknown',
                    'TmDigitalSignature' => $digitalSignature,
                    'OpDigitalSignature' => $digitalSignature
                ],
                [
                    'label' => 'responsible-person-signature',
                    'markup' => TransportManagerSignatureReviewService::SIGNATURE_DIGITAL_BOTH
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


        $tma->shouldReceive('getOpDigitalSignature')->times($conditions['opTimes'])->andReturn($conditions['OpSignature']);
        $tma->shouldReceive('getIsOwner')->once()->andReturn($conditions['isOwner']);
        $tma->shouldReceive('getTmDigitalSignature')->times($conditions['tmTimes'])->andReturn($conditions['tmSignature']);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn(Organisation::ORG_TYPE_SOLE_TRADER);

        $mockTranslator
            ->shouldReceive('translate')
            ->with($conditions['markup'], 'snapshot')
            ->once()
            ->andReturn($expected['replacements']);

        $actual = $this->sut->getConfig($tma);
        $this->assertEquals([
            'markup' => $expected['markup']
        ], $actual);
    }

    public function digitalSignatureDataProvider()
    {
        $opDigitalSignature = m::mock(DigitalSignature::class);
        $opDigitalSignature->shouldReceive('getSignatureName')->andReturn('NAME');
        $opDigitalSignature->shouldReceive('getDateOfBirth')->andReturn('01-01-1980');
        $digitalSignatureDate = (new DateTime('2018-01-01'))->format('d-m-Y');
        $opDigitalSignature->shouldReceive('getCreatedOn')->andReturn($digitalSignatureDate);
        $tmDigitalSignature = m::mock(DigitalSignature::class);
        $tmDigitalSignature->shouldReceive('getSignatureName')->andReturn('NAME');
        $tmDigitalSignature->shouldReceive('getDateOfBirth')->andReturn('01-01-1980');
        $tmDigitalSignature->shouldReceive('getCreatedOn')->andReturn($digitalSignatureDate);
        $emptyDigitalSignature = m::mock(DigitalSignature::class);
        $emptyDigitalSignature->shouldReceive('getSignatureName')->andReturn(false);

        return [

            "Operator and TM" => [
                [
                    'OpSignature' => $opDigitalSignature,
                    'isOwner' => 'N',
                    'tmSignature' => $tmDigitalSignature,
                    "markup" => TransportManagerSignatureReviewService::SIGNATURE_DIGITAL_BOTH,
                    "tmTimes" =>3,
                    "opTimes" =>4
                ],
                [
                    'label' => 'owners-signature',
                    'markup' => 'NAME_01-01-1980_01-01-2018_NAME_01-01-1980_01-01-2018_owners-signaturetranslated_ADDRESS',
                    'replacements'=>'%s_%s_%s_%s_%s_%s_%s_%s',
                ]
            ],
            "as TM only" => [
                [
                    'OpSignature' =>$emptyDigitalSignature,
                    'isOwner' => 'N',
                    'tmSignature' => $tmDigitalSignature,
                    "markup" => TransportManagerSignatureReviewService::SIGNATURE_DIGITAL,
                    "tmTimes" =>3,
                    "opTimes" =>2
                ],
                [
                    'label' => 'owners-signature',
                    'replacements'=>'%s_%s_%s_%s_%s',
                    'markup' => 'NAME_01-01-1980_01-01-2018_owners-signaturetranslated_ADDRESS'
                ]
            ],
            "as OperatorTM" => [
                [
                    'OpSignature' => $opDigitalSignature,
                    'isOwner' => 'Y',
                    'tmSignature' => $tmDigitalSignature,
                    "markup" => TransportManagerSignatureReviewService::SIGNATURE_DIGITAL_OPERATOR_TM,
                    "tmTimes" =>3,
                    "opTimes" =>4
                ],
                [
                    'label' => 'owners-signature',
                    'markup' => 'NAME_01-01-1980_01-01-2018',
                    'replacements'=>'%s_%s_%s',
                ]
            ],

        ];
    }
}
