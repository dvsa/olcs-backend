<?php

namespace Dvsa\OlcsTest\Cli\Domain\QueryHandler\GetDbValue;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Cli\Domain\Exception;
use Dvsa\Olcs\Cli\Domain\QueryHandler\Util\GetDbValue;
use Dvsa\Olcs\Cli\Domain\Query\Util\GetDbValue as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Mockery as m;

/**
 * Class GetDbValueTest
 *
 * @package Dvsa\OlcsTest\Cli\Domain\QueryHandler\
 * use Dvsa\Olcs\Cli\Domain\QueryHandler\Util\GetDbValue
 */
class GetDbValueTest extends QueryHandlerTestCase
{
    /** @var GetDbValue */
    protected $sut;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->sut = new GetDbValue();
        $this->mockRepo('GetDbValue', \Dvsa\Olcs\Api\Domain\Repository\GetDbValue::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        /** @var RefData $refData */
        $refData = m::mock(new RefData('apsts_not_submitted'))->makePartial();
        $application = new Application($licence, $refData, false);

        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'status',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $query = Qry::create($parameters);

        $fullEntityName = $this->sut::ENTITIES_NAMESPACE . $parameters['entityName'];

        $this->repoMap['GetDbValue']
            ->shouldReceive('setEntity')
            ->with($fullEntityName);

        $this->repoMap['GetDbValue']
            ->shouldReceive('fetchOneEntityByX')
            ->with($parameters['filterProperty'], $parameters['filterValue'])
            ->andReturn($application);

        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
    }

    public function testHandleQueryMissingParameters()
    {
        $parameters = [];
        $query = Qry::create($parameters);
        $this->expectException(Exception\MissingParameterException::class);
        $this->sut->handleQuery($query);
    }

    public function testHandleQueryInvalidEntity()
    {
        $parameters = [
            'entityName' => 'SomethingThatDoesNotExist',
            'propertyName' => 'status',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $query = Qry::create($parameters);
        $this->expectException(Exception\InvalidEntityException::class);
        $this->sut->handleQuery($query);
    }

    public function testHandleQueryInvalidPropertyName()
    {
        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'SomethingThatDoesNotExist',
            'filterProperty' => 'id',
            'filterValue' => '1'
        ];

        $query = Qry::create($parameters);
        $this->expectException(Exception\InvalidPropertyException::class);
        $this->sut->handleQuery($query);
    }

    public function testHandleQueryInvalidFilterProperty()
    {
        $parameters = [
            'entityName' => 'Application\Application',
            'propertyName' => 'status',
            'filterProperty' => 'SomethingThatDoesNotExist',
            'filterValue' => '1'
        ];

        $query = Qry::create($parameters);
        $this->expectException(Exception\InvalidPropertyException::class);
        $this->sut->handleQuery($query);
    }
}
