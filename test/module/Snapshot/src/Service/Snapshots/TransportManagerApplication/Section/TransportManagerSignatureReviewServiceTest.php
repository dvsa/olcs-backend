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
        $this->sut = new TransportManagerSignatureReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfig($organistionTypeId, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('markup-tma-declaration-signature', 'snapshot')->once()
            ->andReturn('%s_%s');
        $mockTranslator->shouldReceive('translate')->with('tm-review-return-address', 'snapshot')->once()
            ->andReturn('ADDRESS');

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getType->getId')->with()->once()
            ->andReturn($organistionTypeId);

        $this->assertEquals(['markup' => $expected], $this->sut->getConfig($tma));
    }

    public function provider()
    {
        return [
            ['foobar', 'A responsible person\'s signature_ADDRESS'],
            [Organisation::ORG_TYPE_LLP, 'Director\'s signature_ADDRESS'],
            [Organisation::ORG_TYPE_REGISTERED_COMPANY, 'Director\'s signature_ADDRESS'],
            [Organisation::ORG_TYPE_PARTNERSHIP, 'Partner\'s signature_ADDRESS'],
            [Organisation::ORG_TYPE_SOLE_TRADER, 'Owner\'s signature_ADDRESS'],
        ];
    }
}
