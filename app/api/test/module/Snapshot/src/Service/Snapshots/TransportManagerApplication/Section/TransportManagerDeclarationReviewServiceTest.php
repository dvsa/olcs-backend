<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

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
    public function testGetConfig($isTypeInternal, $niFlag, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('isTypeInternal')->with()->once()->andReturn($isTypeInternal);
        $tma->shouldReceive('getApplication->getNiFlag')->with()->once()->andReturn($niFlag);

        $this->assertEquals(['markup' => $expected], $this->sut->getConfig($tma));
    }

    public function provider()
    {
        return [
            [true, 'Y', 'markup-tma-declaration-internal-ni-translated'],
            [true, 'N', 'markup-tma-declaration-internal-gb-translated'],
            [false, 'Y', 'markup-tma-declaration-external-ni-translated'],
            [false, 'N', 'markup-tma-declaration-external-gb-translated'],
        ];
    }
}
