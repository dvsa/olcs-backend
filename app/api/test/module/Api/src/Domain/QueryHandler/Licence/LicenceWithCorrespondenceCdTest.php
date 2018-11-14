<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\LicenceWithCorrespondenceCd;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Licence\LicenceWithCorrespondenceCd as Qry;


class LicenceWithCorrespondenceCdTest extends QueryHandlerTestCase
{

    public function setUp()
    {
        $this->sut = new LicenceWithCorrespondenceCd();
        $this->mockRepo('Licence', Repository\Licence::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {

        $queryData = ['id' => 1];
        $query = Qry::create($queryData);

        $mockResult = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);
        $this->assertInstanceOf(\Dvsa\Olcs\Api\Domain\QueryHandler\Result::class, $result);
    }
}
