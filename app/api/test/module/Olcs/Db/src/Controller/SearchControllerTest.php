<?php

namespace OlcsTest\Db\Controller;

use Olcs\Db\Controller\SearchController;
use Mockery as m;

/**
 * Class SearchControllerTest
 * @package OlcsTest\Db\Controller
 */
class SearchControllerTest extends \PHPUnit\Framework\TestCase
{
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

        $mockElastic = m::mock('Olcs\Db\Service\Search\Search');
        $mockElastic->shouldReceive('search')->with('test', ['application'], 1, 10)->andReturn('resultSet');
        $mockElastic->shouldReceive('setSort')->with('someField');
        $mockElastic->shouldReceive('setOrder')->with('desc');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ElasticSearch\Search')->andReturn($mockElastic);

        $sut = new SearchController();
        $sut->setPluginManager($mockPluginManager);
        $sut->setServiceLocator($mockSl);

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Results found","Data":"resultSet"}}',
            $sut->getList()->getContent()
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
