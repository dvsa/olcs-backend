<?php

namespace OlcsTest\Db\Controller;

use Olcs\Db\Controller\SearchController;
use Mockery as m;
use Olcs\Db\Service\Search\Search;
use OlcsTest\Bootstrap;

/**
 * Class SearchControllerTest
 * @package OlcsTest\Db\Controller
 */
class SearchControllerTest extends \PHPUnit\Framework\TestCase
{
    protected $mockSearchService;

    protected $sut;

    public function setUp(): void
    {
        $this->mockSearchService = m::mock(Search::class);
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new SearchController($this->mockSearchService);
        $this->sut->setServiceLocator($this->sm);
    }
    public function testGetList()
    {
        $mockPluginManager = $this->getMockPluginManager(['params' => 'Params']);

        $mockParams = $mockPluginManager->get('params', '');
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

        $this->mockSearchService->shouldReceive('search')->with('test', ['application'], 1, 10)->andReturn('resultSet');
        $this->mockSearchService->shouldReceive('setSort')->with('someField');
        $this->mockSearchService->shouldReceive('setOrder')->with('desc');

        $this->sut->setPluginManager($mockPluginManager);

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Results found","Data":"resultSet"}}',
            $this->sut->getList()->getContent()
        );
    }

    /**
     * @param $class
     * @return m\MockInterface
     */
    protected function getMockPlugin($class)
    {
        if (strpos($class, '\\') === false) {
            $class = 'Laminas\Mvc\Controller\Plugin\\' . $class;
        }

        $mockPlugin = m::mock($class);
        $mockPlugin->shouldReceive('__invoke')->andReturnSelf();
        return $mockPlugin;
    }

    /**
     * @param $plugins
     * @return m\MockInterface|\Laminas\Mvc\Controller\PluginManager
     */
    protected function getMockPluginManager($plugins)
    {
        $mockPluginManager = m::mock('Laminas\Mvc\Controller\PluginManager');
        $mockPluginManager->shouldReceive('setController');

        foreach ($plugins as $name => $class) {
            $mockPlugin = $this->getMockPlugin($class);
            $mockPluginManager->shouldReceive('get')->with($name, '')->andReturn($mockPlugin);
        }

        return $mockPluginManager;
    }
}
