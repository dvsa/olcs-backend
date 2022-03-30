<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerDeclarationReviewService;
use OlcsTest\Bootstrap;

/**
 * TransportManagerDeclarationReviewServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerDeclarationReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new TransportManagerDeclarationReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfig(
        $isTypeInternal,
        $niFlag,
        $goodsOrPsvId,
        $expectedMarkupTranslationKey,
        $expectedResidencyClauseKey,
        $expectedRoleClauseKey
    ) {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $translatedResidencyClauseKey = 'translated residency clause key';
        $translatedRoleClauseKey = 'translated role clause key';

        $mockTranslator->shouldReceive('translate')
            ->with($expectedResidencyClauseKey)
            ->andReturn($translatedResidencyClauseKey);
        $mockTranslator->shouldReceive('translate')
            ->with($expectedRoleClauseKey)
            ->andReturn($translatedRoleClauseKey);
        $mockTranslator->shouldReceive('translate')
            ->with($expectedMarkupTranslationKey)
            ->andReturn('translated and replaced [%s] [%s]');

        $application = m::mock(Application::class);
        $application->shouldReceive('getNiFlag')
            ->withNoArgs()
            ->andReturn($niFlag);
        $application->shouldReceive('getGoodsOrPsv->getId')
            ->withNoArgs()
            ->andReturn($goodsOrPsvId);

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getApplication')
            ->withNoArgs()
            ->andReturn($application);
        $tma->shouldReceive('isTypeInternal')
            ->withNoArgs()
            ->andReturn($isTypeInternal);

        $expectedMarkup = 'translated and replaced [translated residency clause key] [translated role clause key]';

        $this->assertEquals(['markup' => $expectedMarkup], $this->sut->getConfig($tma));
    }

    public function provider()
    {
        return [
            [
                true,
                'Y',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'markup-tma-declaration-internal-ni',
                'tma-declaration.residency-clause.lcat_gv',
                'tma-declaration.role-clause.lcat_gv'
            ],
            [
                true,
                'N',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'markup-tma-declaration-internal-gb',
                'tma-declaration.residency-clause.lcat_gv',
                'tma-declaration.role-clause.lcat_gv'
            ],
            [
                false,
                'Y',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'markup-tma-declaration-external-ni',
                'tma-declaration.residency-clause.lcat_gv',
                'tma-declaration.role-clause.lcat_gv'
            ],
            [
                false,
                'N',
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'markup-tma-declaration-external-gb',
                'tma-declaration.residency-clause.lcat_gv',
                'tma-declaration.role-clause.lcat_gv'
            ],
            [
                true,
                'Y',
                Licence::LICENCE_CATEGORY_PSV,
                'markup-tma-declaration-internal-ni',
                'tma-declaration.residency-clause.lcat_psv',
                'tma-declaration.role-clause.lcat_psv'
            ],
            [
                true,
                'N',
                Licence::LICENCE_CATEGORY_PSV,
                'markup-tma-declaration-internal-gb',
                'tma-declaration.residency-clause.lcat_psv',
                'tma-declaration.role-clause.lcat_psv'
            ],
            [
                false,
                'Y',
                Licence::LICENCE_CATEGORY_PSV,
                'markup-tma-declaration-external-ni',
                'tma-declaration.residency-clause.lcat_psv',
                'tma-declaration.role-clause.lcat_psv'
            ],
            [
                false,
                'N',
                Licence::LICENCE_CATEGORY_PSV,
                'markup-tma-declaration-external-gb',
                'tma-declaration.residency-clause.lcat_psv',
                'tma-declaration.role-clause.lcat_psv'
            ],
        ];
    }
}
