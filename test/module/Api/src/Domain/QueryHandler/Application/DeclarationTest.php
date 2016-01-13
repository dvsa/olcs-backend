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

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockApplication);

        $mockApplication->shouldReceive('serialize')->once()->andReturn(['foo' => 'bar']);
        $mockApplication->shouldReceive('canHaveInterimLicence')->with()->once()->andReturn('xxx');
        $mockApplication->shouldReceive('isLicenceUpgrade')->with()->once()->andReturn('yyy');

        $expected = [
            'foo' => 'bar',
            'canHaveInterimLicence' => 'xxx',
            'isLicenceUpgrade' => 'yyy',
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
