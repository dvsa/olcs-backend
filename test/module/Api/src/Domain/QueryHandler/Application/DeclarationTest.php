<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Declaration;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\Olcs\Api\Entity\System\SystemParameter as SystemParameterEntity;
use Dvsa\Olcs\Transfer\Query\Application\Declaration as Qry;
use Dvsa\Olcs\Api\Service\FeesHelperService;
use Dvsa\Olcs\Api\Service\Lva\SectionAccessService;
use Mockery as m;

/**
 * DeclarationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeclarationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Declaration();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);
        $this->mockedSmServices = [
            'FeesHelperService' => m::mock(FeesHelperService::class),
            'SectionAccessService' => m::mock(SectionAccessService::class),
            'Review\ApplicationUndertakings' => m::mock(),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class)
            ->shouldReceive('getId')
            ->andReturn(111)
            ->once()
            ->shouldReceive('getVariationCompletion')
            ->andReturn('foo')
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockApplication);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(SystemParameterEntity::DISABLE_GDS_VERIFY_SIGNATURES)
            ->andReturn(true)
            ->once();

        $mockApplication->shouldReceive('serialize')->twice()->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('canHaveInterimLicence')->with()->once()->andReturn('xxx');
        $mockApplication->shouldReceive('isLicenceUpgrade')->with()->once()->andReturn('yyy');
        $mockApplication->shouldReceive('isGoods')->with()->once()->andReturn(true);

        $this->mockedSmServices['FeesHelperService']
            ->shouldReceive('getTotalOutstandingFeeAmountForApplication')
            ->with(111)
            ->andReturn(123.45)
            ->once()
            ->getMock();

        $this->mockedSmServices['SectionAccessService']
            ->shouldReceive('getAccessibleSections')
            ->with($mockApplication)
            ->andReturn('bar')
            ->once()
            ->getMock();

        $this->mockedSmServices['Review\ApplicationUndertakings']
            ->shouldReceive('getMarkup')
            ->with(['foo' => 'bar', 'isGoods' => true, 'isInternal' => false])
            ->once()
            ->andReturn('markup')
            ->getMock();

        $expected = [
            'foo' => 'bar',
            'canHaveInterimLicence' => 'xxx',
            'isLicenceUpgrade' => 'yyy',
            'outstandingFeeTotal' => 123.45,
            'sections' => 'bar',
            'variationCompletion' => 'foo',
            'disableSignatures' => true,
            'declarations' => 'markup',
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
