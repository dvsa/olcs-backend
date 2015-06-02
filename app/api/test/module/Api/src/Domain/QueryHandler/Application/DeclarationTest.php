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

        $mockApplication = \Mockery::mock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockApplication);

        $mockApplication->shouldReceive('jsonSerialize')->with()->once()->andReturn(['foo' => 'bar']);

        $mockApplication->shouldReceive('canHaveInterimLicence')->with()->once()->andReturn('xxx');
        $mockApplication->shouldReceive('isLicenceUpgrade')->with()->once()->andReturn('yyy');

        $expected = [
            'foo' => 'bar',
            'canHaveInterimLicence' => 'xxx',
            'isLicenceUpgrade' => 'yyy',
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
