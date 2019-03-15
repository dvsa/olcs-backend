<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
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

    public function setUp()
    {
        $this->sut = new SignatureReviewService();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testPhysicalSignature()
    {
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

        $signatureType = m::mock(RefData::class);
        $signatureType->shouldReceive('getId')
            ->andReturn(RefData::SIG_PHYSICAL_SIGNATURE);

        $markup = $this->sut->getConfigFromData([
            'signatureType' => $signatureType,
            'digitalSignature' => null
        ]);
        $expected = [
            'markup' => "directors-signature-translated__return-address-translated__translated"
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
            ->with('markup-signature-digital', 'snapshot')
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
}
