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
 * use Dvsa\Olcs\Cli\Domain\QueryHandler\Util\GetDbValue
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
        $this->mockRepo('GetDbValue', \Dvsa\Olcs\Api\Domain\Repository\GetDbValue::class);
        parent::setup();
    }

    public function testHandleQuery()
    {

        $this->mockRepo('Application', Application::class);

        $query = Qry::create(
            [
                'tableName' => 'Application',
                'columnName' => 'id',
                'filterName' => 'id',
                'filterValue' => '1'
            ]
        );


        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
    }
}
