<?php

namespace OlcsTest\Db\Controller;

use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Controller\PluginManager;
use Olcs\Db\Controller\SearchController;
use Mockery as m;
use Olcs\Db\Service\Search\Search;

class SearchControllerTest extends m\Adapter\Phpunit\MockeryTestCase
{
    protected $mockSearchService;

    protected $sut;

    public function setUp(): void
    {
        $this->mockSearchService = m::mock(Search::class);
        $this->sut = new SearchController($this->mockSearchService);
    }
    public function testGetList()
    {
        $mockParams = m::mock(Params::class);
        $mockParams->expects('__invoke')->twice()->andReturnSelf();
        $mockParams->shouldReceive('fromRoute')->andReturn([]);
        $mockParams->shouldReceive('fromQuery')->andReturn(
            [
                'q' => 'test',
                'index' => 'application',
                'page' => 1,
                'limit' => 10,
                'sort' => 'someField',
                'order' => 'desc'
            ]
        );

        $mockPluginManager = m::mock(PluginManager::class);
        $mockPluginManager->expects('setController')->times(3);
        $mockPluginManager->expects('get')->with('params', null)->times(2)->andReturn($mockParams);

        $this->mockSearchService->shouldReceive('search')->with('test', ['application'], 1, 10)->andReturn('resultSet');
        $this->mockSearchService->shouldReceive('setSort')->with('someField');
        $this->mockSearchService->shouldReceive('setOrder')->with('desc');

        $this->sut->setPluginManager($mockPluginManager);

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Results found","Data":"resultSet"}}',
            $this->sut->getList()->getContent()
        );
    }
}
