<?php

namespace Dvsa\OlcsTest\Api\Mvc\Controller\Plugin;

use Dvsa\Olcs\Api\Domain\Command\Result as CommandResult;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result as QueryResult;
use Dvsa\Olcs\Api\Entity\Venue as VenueEntity;
use Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response as Sut;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model\JsonModel;

/**
 * Response Test
 */
class ResponseTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->response = new HttpResponse();

        $this->sut = m::mock(Sut::class)->makePartial();
        $this->sut->shouldReceive('getController->getResponse')->andReturn($this->response);

        parent::setUp();
    }

    public function testNotFound()
    {
        $result = $this->sut->notFound();

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_404, $result->getStatusCode());
    }

    public function testNotReady()
    {
        $result = $this->sut->notReady();

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_503, $result->getStatusCode());
        $this->assertNotContains('Retry-After', $result->getHeaders()->toArray());
    }

    public function testNotReadyWithRetryAfter()
    {
        $retryAfter = 5;

        $result = $this->sut->notReady($retryAfter);

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_503, $result->getStatusCode());
        $this->assertEquals(['Retry-After' => $retryAfter], $result->getHeaders()->toArray());
    }

    public function testError()
    {
        $result = $this->sut->error(HttpResponse::STATUS_CODE_400);

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_400, $result->getStatusCode());
    }

    public function testErrorWithMessages()
    {
        $messages = ['MSG1'];

        $result = $this->sut->error(HttpResponse::STATUS_CODE_400, $messages);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('{"messages":["MSG1"]}', $result->serialize());
        $this->assertEquals(HttpResponse::STATUS_CODE_400, $this->response->getStatusCode());
    }

    /**
     * @dataProvider getSingleResultDataProvider
     */
    public function testSingleResult($data)
    {
        $result = $this->sut->singleResult($data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('["item"]', $result->serialize());
        $this->assertEquals(HttpResponse::STATUS_CODE_200, $this->response->getStatusCode());
    }

    public function getSingleResultDataProvider()
    {
        return [
            // array
            [
                ['item']
            ],
            // QueryResult
            [
                m::mock(QueryResult::class)->shouldReceive('serialize')->andReturn(['item'])->getMock()
            ],
            // Entity
            [
                m::mock(VenueEntity::class)->shouldReceive('jsonSerialize')->andReturn(['item'])->getMock()
            ],
        ];
    }

    public function testMultipleResults()
    {
        $result = $this->sut->multipleResults(2, ['item1', 'item2'], 10, ['extra1']);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals(
            '{"count":2,"results":["item1","item2"],"count-unfiltered":10,"extra":["extra1"]}',
            $result->serialize()
        );
        $this->assertEquals(HttpResponse::STATUS_CODE_200, $this->response->getStatusCode());
    }

    public function testSuccessfulUpdate()
    {
        $data = new CommandResult();
        $data->addId('item', 1);
        $data->addMessage('msg');

        $result = $this->sut->successfulUpdate($data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('{"id":{"item":1},"messages":["msg"]}', $result->serialize());
        $this->assertEquals(HttpResponse::STATUS_CODE_200, $this->response->getStatusCode());
    }

    public function testSuccessfulCreate()
    {
        $data = new CommandResult();
        $data->addId('item', 1);
        $data->addMessage('msg');

        $result = $this->sut->successfulCreate($data);

        $this->assertInstanceOf(JsonModel::class, $result);
        $this->assertEquals('{"id":{"item":1},"messages":["msg"]}', $result->serialize());
        $this->assertEquals(HttpResponse::STATUS_CODE_201, $this->response->getStatusCode());
    }

    public function testXmlAccepted()
    {
        $result = $this->sut->xmlAccepted();

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_202, $result->getStatusCode());
    }

    public function testXmlBadRequest()
    {
        $result = $this->sut->xmlBadRequest();

        $this->assertInstanceOf(HttpResponse::class, $result);
        $this->assertEquals(HttpResponse::STATUS_CODE_400, $result->getStatusCode());
    }
}
