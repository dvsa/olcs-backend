<?php

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as Qry;

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Licence();
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
