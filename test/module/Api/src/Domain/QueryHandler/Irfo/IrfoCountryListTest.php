<?php

/**
 * IrfoCountryList test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\Irfo\IrfoCountryList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrfoCountry as IrfoCountryRepo;
use Dvsa\Olcs\Transfer\Query\Irfo\IrfoCountryList as Qry;

/**
 * IrfoCountryList test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IrfoCountryListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new IrfoCountryList();
        $this->mockRepo('IrfoCountry', IrfoCountryRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $this->repoMap['IrfoCountry']->shouldReceive('fetchList')
            ->with($query)
            ->andReturn(['foo']);

        $this->repoMap['IrfoCountry']->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], 2);
        $this->assertEquals($result['result'], ['foo']);
    }
}
