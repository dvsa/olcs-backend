<?php

namespace OlcsTest\Db\Controller;

use Olcs\Db\Controller\RefDataController;

class RefDataControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $lang = 'en_GB';
        $category = 'category';
        $data = [['id' => 'category.1', 'description' => 'First Category']];

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

        $sut = new RefDataController();

        $routeMatch = new \Zend\Mvc\Router\RouteMatch(array('lang'=> $lang));
        $sut->getEvent()->setRouteMatch($routeMatch);

        $sut->setServiceLocator($mockSl);
        $response = $sut->get($category);

        $responseArray = json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY);

        $this->assertEquals($data, $responseArray['Response']['Data']);

    }
}
 