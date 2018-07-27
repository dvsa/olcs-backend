<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerSignatureReviewService;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * TransportManagerSignatureReviewServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerSignatureReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new TransportManagerSignatureReviewService($this->sm);
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfig($organistionTypeId, $label)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('markup-tma-declaration-signature', 'snapshot')->once()
            ->andReturn('%s_%s');
        $mockTranslator->shouldReceive('translate')->with('tm-review-return-address', 'snapshot')->once()
            ->andReturn('ADDRESS');

        $mockTranslator->shouldReceive('translate')->with($label, 'snapshot')->once()
            ->andReturn($label .'translated');

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn($organistionTypeId);

        $expected = $label .'translated_ADDRESS';
        $this->assertEquals(['markup' => $expected], $this->sut->getConfig($tma));
    }

    public function provider()
    {
        return [
            ['foobar', 'responsible-person-signature'],
            [Organisation::ORG_TYPE_LLP, 'directors-signature'],
            [Organisation::ORG_TYPE_REGISTERED_COMPANY, 'directors-signature'],
            [Organisation::ORG_TYPE_PARTNERSHIP, 'partners-signature'],
            [Organisation::ORG_TYPE_SOLE_TRADER, 'owners-signature'],
        ];
    }
}
