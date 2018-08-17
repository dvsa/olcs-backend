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
            $mockTranslator->shouldReceive('translate')->with($expected['signatureBoxPartial'], 'snapshot')->once()
                ->andReturn('%s_%s');
        }

        $mockTranslator->shouldReceive('translate')->with(TransportManagerSignatureReviewService::ADDRESS, 'snapshot')->once()
            ->andReturn('ADDRESS');

        $mockTranslator->shouldReceive('translate')->with($expected['label'], 'snapshot')->once()
            ->andReturn($expected['label'] .'translated');

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getDigitalSignature')->andReturn($data['digitalSignature']);

        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn($data['organisationType']);

        $expectedMarkup = $expected['label'] .'translated_ADDRESS';

        if ($data['digitalSignature']) {
            $digitalSignatureDate = new DateTime('2018-01-01');
            $data['digitalSignature']
                ->shouldReceive('getCreatedOn')
                ->with(true)
                ->andReturn($digitalSignatureDate);
            $name = 'Name';
            $familyName = 'FamilyName';
            $birthDate = new DateTime('1980-01-01');
            $tm = m::mock(TransportManager::class);
            $contactDetails = m::mock(ContactDetails::class);
            $person = m::mock(Person::class);
            $tm->shouldReceive('getHomeCd')->andReturn($contactDetails);
            $contactDetails->shouldReceive('getPerson')->andReturn($person);
            $person->shouldReceive('getBirthDate')->with(true)->andReturn($birthDate);
            $person->shouldReceive('getTitle')->andReturn(null);
            $person->shouldReceive('getForename')->andReturn($name);
            $person->shouldReceive('getFamilyName')->andReturn($familyName);
            $tma->shouldReceive('getTransportManager')->andReturn($tm);
            $mockTranslator->shouldReceive('translate')->with($expected['signatureBoxPartial'], 'snapshot')->once()
                ->andReturn('%s_%s_%s_%s_%s');
            $expectedMarkup = $name .
                ' ' . $familyName .
                '_' . $birthDate->format('d-m-Y') .
                '_' . $digitalSignatureDate->format('d-m-Y') .
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
}
