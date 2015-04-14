<?php

namespace OlcsTest\Db\Controller;

use Olcs\Db\Controller\RefDataController;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;

/**
 * Class RefDataControllerTest
 * @package OlcsTest\Db\Controller
 */
class RefDataControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $lang = 'en_GB';
        $id = 'category';
        $data = [['id' => 'category.1', 'description' => 'First Category']];

        // -- Start Doctrine Mocking

        $mockRepo = $this->getMock('\Olcs\Db\Entity\Repository\RefData', [], [], '', 0);
        $mockRepo->expects($this->once())
            ->method('findByIdentifierAndLanguage')
            ->with($this->equalTo($id), $this->equalTo($lang))
            ->willReturn($data);

        $mockEm = $this->getMock('\Doctrine\ORM\EntityManagerInterface');
        $mockEm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Olcs\Db\Entity\RefData'))
            ->willReturn($mockRepo);

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.entitymanager.orm_default'))
            ->willReturn($mockEm);

        // -- End Doctrine Mocking

        $sut = new RefDataController();

        $request = m::mock(Request::class);
        $request->shouldReceive('getHeaders')->andReturnSelf();
        $request->shouldReceive('getFieldValue')->once()->andReturn($lang);

        $sut->getEvent()->setRequest($request);
        $sut->setServiceLocator($mockSl);

        $response = $sut->get($id);

        $responseArray = json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $responseArray['Response']['Data']);
    }

    public function testGetList()
    {
        $lang = 'en_GB';
        $category = 'category';
        $data = [['id' => 'category.1', 'description' => 'First Category']];

        // -- Start Doctrine Mocking

        $mockRepo = $this->getMock('\Olcs\Db\Entity\Repository\RefData', [], [], '', 0);
        $mockRepo->expects($this->once())
            ->method('findAllByCategoryAndLanguage')
            ->with($this->equalTo($category), $this->equalTo($lang))
            ->willReturn($data);

        $mockEm = $this->getMock('\Doctrine\ORM\EntityManagerInterface');
        $mockEm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Olcs\Db\Entity\RefData'))
            ->willReturn($mockRepo);

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->expects($this->once())
            ->method('get')
            ->with($this->equalTo('doctrine.entitymanager.orm_default'))
            ->willReturn($mockEm);

        // -- End Doctrine Mocking

        $sut = new RefDataController();

        $request = m::mock(Request::class);
        $request->shouldReceive('getHeaders')->andReturnSelf();
        $request->shouldReceive('getFieldValue')->once()->andReturn($lang);

        $sut->getEvent()->setRequest($request);
        $sut->setServiceLocator($mockSl);

        $routeMatch = new RouteMatch([]);
        $routeMatch->setParam('category', $category);
        $sut->getEvent()->setRouteMatch($routeMatch);

        //$params->setController($sut);
        //$sut->getPluginManager()->setService('params', $params);

        $response = $sut->getList($category);

        $responseArray = json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $responseArray['Response']['Data']);
    }
}
