<?php

/**
 * Tests BookmarkSearchController
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\BookmarkSearchController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests BookmarkSearchController
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BookmarkSearchControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\BookmarkSearchController',
            array(
                'respond',
                'getDataFromQuery',
                'getService',
                'getLogger'
            )
        );
    }

    /**
     * Test getList
     *  with exception
     */
    public function testGetListWithNoBundleThrowsException()
    {
        $options = array(
            'foo' => 'bar'
        );

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        try {
            $this->controller->getList();
        } catch (RestResponseException $e) {
            $this->assertEquals(
                'Please provide a bundle',
                $e->getMessage()
            );
            return;
        }

        $this->fail('Expected exception not raised');
    }

    /**
     * Test getList
     */
    public function testGetList()
    {
        $bundle = array(
            'paragraphs' => array(
                array(
                    'service' => 'DocParagraph',
                    'data' => [
                        'id' => 1
                    ],
                    'bundle' => []
                ),
                array(
                    'service' => 'DocParagraph',
                    'data' => [
                        'id' => 2
                    ],
                    'bundle' => []
                )
            ),
            'loop' => array(
                'service' => 'Loop',
                'options' => [
                    'loop' => true
                ],
                'data' => [
                    ['id' => 123],
                    ['id' => 321],
                ],
                'bundle' => []
            ),
            'details' => array(
                'service' => 'User',
                'data' => [
                    'id' => 123,
                ],
                'bundle' => []
            ),
            'list' => array(
                'service' => 'Task',
                'data' => [],
                'bundle' => []
            )
        );
        $options = array(
            'bundle' => json_encode($bundle)
        );

        $mockParaService = $this->getMock('\stdClass', array('get'));

        $mockParaService->expects($this->at(0))
            ->method('get')
            ->with(1)
            ->willReturn(['text' => 'foo']);

        $mockParaService->expects($this->at(1))
            ->method('get')
            ->with(2)
            ->willReturn(['text' => 'bar']);

        $mockLoopService = $this->getMock('\stdClass', array('get'));

        $mockLoopService->expects($this->at(0))
            ->method('get')
            ->with(123)
            ->willReturn(['text' => 'foo']);

        $mockLoopService->expects($this->at(1))
            ->method('get')
            ->with(321)
            ->willReturn(['text' => 'bar']);

        $mockUserService = $this->getMock('\stdClass', array('get'));

        $mockUserService->expects($this->once())
            ->method('get')
            ->with(123)
            ->willReturn(['name' => 'user']);

        $mockTaskService = $this->getMock('\stdClass', array('getList'));

        $mockTaskService->expects($this->once())
            ->method('getList')
            ->with(['bundle' => '[]'])
            ->willReturn(
                [
                    ['name' => 'task 1']
                ]
            );

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $this->controller->expects($this->at(1))
            ->method('getService')
            ->with('DocParagraph')
            ->will($this->returnValue($mockParaService));

        $this->controller->expects($this->at(2))
            ->method('getService')
            ->with('DocParagraph')
            ->will($this->returnValue($mockParaService));

        $this->controller->expects($this->at(3))
            ->method('getService')
            ->with('Loop')
            ->will($this->returnValue($mockLoopService));

        $this->controller->expects($this->at(4))
            ->method('getService')
            ->with('Loop')
            ->will($this->returnValue($mockLoopService));

        $this->controller->expects($this->at(5))
            ->method('getService')
            ->with('User')
            ->will($this->returnValue($mockUserService));

        $this->controller->expects($this->at(6))
            ->method('getService')
            ->with('Task')
            ->will($this->returnValue($mockTaskService));

        $expectedResult = [
            'paragraphs' => [
                ['text' => 'foo'],
                ['text' => 'bar']
            ],
            'loop' => [
                ['text' => 'foo'],
                ['text' => 'bar']
            ],
            'details' => [
                'name' => 'user'
            ],
            'list' => [
                [
                    'name' => 'task 1'
                ]
            ]
        ];
        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, 'OK', $expectedResult);

        $this->controller->getList();
    }

    public function testGetListWithException()
    {
        $bundle = array(
            'list' => array(
                'service' => 'Task',
                'data' => [],
                'bundle' => []
            )
        );
        $options = array(
            'bundle' => json_encode($bundle)
        );

        $mockTaskService = $this->getMock('\stdClass', array('getList'));

        $mockTaskService->expects($this->once())
            ->method('getList')
            ->will($this->throwException(new \Exception('error')));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $this->controller->expects($this->at(1))
            ->method('getService')
            ->with('Task')
            ->will($this->returnValue($mockTaskService));

        $logger = $this->getMock('\stdClass', ['info']);
        $logger->expects($this->once())
            ->method('info');

        $this->controller->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);

        try {
            $this->controller->getList();
        } catch (RestResponseException $e) {
            $this->assertEquals(
                'error',
                $e->getMessage()
            );
            return;
        }

        $this->fail('Expected exception not raised');
    }
}
