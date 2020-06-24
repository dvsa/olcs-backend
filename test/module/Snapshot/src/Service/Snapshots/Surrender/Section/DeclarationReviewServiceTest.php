<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DeclarationReviewService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use OlcsTest\Bootstrap;

class DeclarationReviewServiceTest extends MockeryTestCase
{
    use \Dvsa\Olcs\Snapshot\Service\Snapshots\FormatReviewDataTrait;

    /** @var DeclarationReviewService */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new DeclarationReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $mockEntity = m::mock(Surrender::class);

        $mockEntity->shouldReceive('getLicence->getLicNo')->andReturn(7)->once();

        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($expected) {
                    return $expected . '-translated';
                }
            );

        $expected = ['markup' => 'markup-licence-surrender-declaration-translated'];

        $this->assertEquals($expected, $this->sut->getConfigFromData($mockEntity));
    }
}
