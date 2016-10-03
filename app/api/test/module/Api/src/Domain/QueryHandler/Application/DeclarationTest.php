<?php

/**
 * DeclarationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Declaration;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
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
        $this->mockedSmServices = [
            'FeesHelperService' => m::mock(FeesHelperService::class),
            'SectionAccessService' => m::mock(SectionAccessService::class),
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

        $mockApplication->shouldReceive('serialize')->once()->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('canHaveInterimLicence')->with()->once()->andReturn('xxx');
        $mockApplication->shouldReceive('isLicenceUpgrade')->with()->once()->andReturn('yyy');

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

        $expected = [
            'foo' => 'bar',
            'canHaveInterimLicence' => 'xxx',
            'isLicenceUpgrade' => 'yyy',
            'outstandingFeeTotal' => 123.45,
            'sections' => 'bar',
            'variationCompletion' => 'foo',
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
