<?php

namespace Dvsa\OlcsTest\Cli\Domain\QueryHandler\GetDbValue;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Cli\Domain\QueryHandler\Util\GetDbValue;
use Dvsa\Olcs\Cli\Domain\Query\Util\GetDbValue as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;


/**
 * Class GetDbValueTest
 *
 * @package Dvsa\OlcsTest\Cli\Domain\QueryHandler\
 * use Dvsa\Olcs\Cli\Domain\QueryHandler\Util\getDbValue
 */
class GetDbValueTest extends QueryHandlerTestCase
{
    protected $sut;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->sut = new GetDbValue();
        parent::setup();
    }

    public function testHandleQuery()
    {

        $this->mockRepo('AbstractApplication', pplication::class);
        $query = Qry::create(
            [
                'tableName' => 'Trailer',
                'columnName' => 'id',
                'filterName' => '',
                'filterValue' => ''
            ]
        );

        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
    }
}
