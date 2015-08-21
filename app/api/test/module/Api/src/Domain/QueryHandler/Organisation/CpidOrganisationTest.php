<?php

/**
 * CpidOrganisationTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace module\Api\src\Domain\QueryHandler\Organisation;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\CpidOrganisation;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Transfer\Query\Organisation\CpidOrganisation as Qry;

/**
 * CpidOrganisationExportTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CpidOrganisation();
        $this->mockRepo('Organisation', Organisation::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = [
            'cpid' => null
        ];

        $query = Qry::create($data);

        $this->repoMap['Organisation']
            ->shouldReceive('fetchByStatusPaginated')
            ->with($query);

        $this->assertEquals(
            $this->sut->handleQuery($query),
            [
                'result' => null,
                'count' => null
            ]
        );
    }
}
