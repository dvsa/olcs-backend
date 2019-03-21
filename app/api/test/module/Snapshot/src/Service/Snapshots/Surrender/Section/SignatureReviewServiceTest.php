<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\SignatureReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

class SignatureReviewServiceTest extends MockeryTestCase
{
    /**
     * @var SignatureReviewService
     */
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new SignatureReviewService();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testPhysicalSignature()
    {
        $surrender = $this->mockSurrender(RefData::SIG_PHYSICAL_SIGNATURE);

        $translator = $this->mockTranslator();
        
        $translator->shouldReceive('translate')
            ->with('markup-signature-physical', 'snapshot')
            ->andReturn('%s__%s__translated');

        $translator->shouldReceive('translate')
            ->with('directors-signature', 'snapshot')
            ->andReturn('directors-signature-translated');

        $translator->shouldReceive('translate')
            ->with('return-address', 'snapshot')
            ->andReturn('return-address-translated');


        $markup = $this->sut->getConfigFromData($surrender);
        $expected = [
            'markup' => "directors-signature-translated__return-address-translated__translated"
        ];

        $this->assertEquals($expected, $markup);
    }

    public function testDigitalSignature()
    {
        $date = new \DateTime("2019-01-29");
        $signature = m::mock(DigitalSignature::class);
        $signature->shouldReceive('getSignatureName')->andReturn('test-name');
        $signature->shouldReceive('getDateOfBirth')->andReturn($date);
        $signature->shouldReceive('getCreatedOn')->andReturn($date);

        $surrender = $this->mockSurrender(RefData::SIG_DIGITAL_SIGNATURE);
        $surrender->shouldReceive('getDigitalSignature')->andReturn($signature);

        $translator = $this->mockTranslator();
        $translator->shouldReceive('translate')
            ->with('markup-signature-digital', 'snapshot')
            ->andReturn('%s__%s__%s__translated');

        $markup = $this->sut->getConfigFromData($surrender);
        $expected = ['markup' => "test-name__29 Jan 2019__29 Jan 2019__translated"];

        $this->assertEquals($expected, $markup);
    }

    protected function mockSurrender($signatureType)
    {
        $refData = m::mock(RefData::class);
        $refData->shouldReceive('getId')->andReturn($signatureType);
        $surrender = m::mock(Surrender::class);
        $surrender->shouldReceive('getSignatureType')->andReturn($refData);

        return $surrender;
    }

    protected function mockTranslator()
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);
        return $mockTranslator;
    }
}
