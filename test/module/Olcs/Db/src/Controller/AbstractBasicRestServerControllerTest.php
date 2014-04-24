<?php

/**
 * Tests AbstractBasicRestServerController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\AbstractBasicRestServerController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests AbstractBasicRestServerController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractBasicRestServerControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Create a mock
     *
     * @param array $methods
     */
    protected function getMockController($methods = array())
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMockForAbstractClass(
            '\Olcs\Db\Controller\AbstractBasicRestServerController',
            array(),
            '',
            true,
            true,
            true,
            // Mocked methods
            $methods
        );
    }

    /**
     * Test checkMethod
     *  Default enabled methods
     *
     * @dataProvider providerForCheckMethod
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testCheckMethodDefaultEnabledMethods($method, $expected)
    {
        $this->getMockController();

        $this->assertEquals($expected, $this->controller->checkMethod($method));
    }

    /**
     * Test checkMethod
     *  With methods disabled
     *
     * @dataProvider providerForCheckMethod
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     *
     * @expectedException \Olcs\Db\Exceptions\RestResponseException
     */
    public function testCheckMethodDisabledMethods($method, $expected)
    {
        $this->getMockController();

        $this->controller->setAllowedMethods(array());

        $this->assertEquals($expected, $this->controller->checkMethod($method));
    }

    /**
     * Provider for checkMethodSuccess
     *
     * @return array
     */
    public function providerForCheckMethod()
    {
        return array(
            array('create', true),
            array('get', true),
            array('getList', true),
            array('update', true),
            array('patch', true),
            array('delete', true),
            array('SomeController::delete', true)
        );
    }

    /**
     * Test getDataFromQuery
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetDataFromQuery()
    {
        $route = array(
            'route' => 'foo',
            'foo' => 'bar'
        );

        $query = array(
            'query' => 'cake',
            'foo' => 'cake'
        );

        $expected = array(
            'route' => 'foo',
            'foo' => 'cake',
            'query' => 'cake'
        );

        $mockPlugin = $this->getMock('\stdClass', array('fromRoute', 'fromQuery'));

        $mockPlugin->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue($route));

        $mockPlugin->expects($this->once())
            ->method('fromQuery')
            ->will($this->returnValue($query));

        $this->getMockController(array('plugin'));

        $this->controller->expects($this->any())
            ->method('plugin')
            ->will($this->returnValue($mockPlugin));

        $this->assertEquals($expected, $this->controller->getDataFromQuery());
    }

    /**
     * Test formatDataFromJson
     *
     * @dataProvider providerFormatDataFromJson
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testFormatDataFromJson($input, $success, $expected)
    {
        $this->getMockController(array('respond'));

        // We expect a response if unsuccsesful
        if ($success === false) {
            $this->controller->expects($this->once())
                ->method('respond')
                ->with(Response::STATUS_CODE_400);
        }

        $response = $this->controller->formatDataFromJson($input);

        if ($success) {
            $this->assertEquals($expected, $response);
        }
    }

    /**
     * Provider for formatDataFromJson
     *
     * @return array
     */
    public function providerFormatDataFromJson()
    {
        return array(
            // None strings
            array(array('data' => array()), false, null),
            array(array(), false, null),
            array(array('data' => new \stdClass()), false, null),
            array(new \stdClass(), false, null),
            // Invalid JSON
            array(array('data' => 'foo'), false, null),
            array('foo', false, null),
            // Valid JSON
            array(array('data' => '{"foo":"bar"}'), true, array('foo' => 'bar')),
            array(array('data' => '{"foo":"bar","cake":[1,2,3]}'), true, array('foo' => 'bar', 'cake' => array(1, 2, 3)))
        );
    }

    /**
     * Test create
     *  With invalid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testCreateWithInvalidData()
    {
        $data = array();

        $mockData = $this->getMock('\Zend\Http\Response');

        $this->getMockController(array('checkMethod', 'formatDataFromJson'));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($mockData));

        $this->assertEquals($mockData, $this->controller->create($data));
    }

    /**
     * Test create
     *  With valid json data
     *  Throw Exception
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testCreateWithValidJsonDataThrowException()
    {
        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $data = array(
            'foo' => 'bar'
        );

        $mockService = $this->getMock('\stdClass', array('create'));

        $mockService->expects($this->once())
            ->method('create')
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->create($data);
    }

    /**
     * Test create
     *  With valid json data
     *  With unexpected id returned
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testCreateWithValidJsonDataUnexpectedID()
    {
        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $data = array(
            'foo' => 'bar'
        );

        $mockService = $this->getMock('\stdClass', array('create'));

        $mockService->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->create($data);
    }

    /**
     * Test create
     *  With valid json data
     *  With expected id returned
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testCreateWithValidJsonDataExpectedID()
    {
        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $data = array(
            'foo' => 'bar'
        );

        $mockService = $this->getMock('\stdClass', array('create'));

        $mockService->expects($this->once())
            ->method('create')
            ->will($this->returnValue(20));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_201, 'Entity Created', array('id' => 20));

        $this->controller->create($data);
    }

    /**
     * Test get
     *  Empty result
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetEmptyResult()
    {
        $data = array('foo' => 'bar');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('get'));

        $mockService->expects($this->once())
            ->method('get')
            ->with(20, $data)
            ->will($this->returnValue(null));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->get(20);
    }

    /**
     * Test get
     *  With Result
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetWithResult()
    {
        $data = array('foo' => 'bar');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('get'));

        $mockService->expects($this->once())
            ->method('get')
            ->with(20, $data)
            ->will($this->returnValue(array('foo' => 'bar')));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, 'Entity found', array('foo' => 'bar'));

        $this->controller->get(20);
    }

    /**
     * Test get
     *  Throws Exception
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetThrowsException()
    {
        $data = array('foo' => 'bar');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('get'));

        $mockService->expects($this->once())
            ->method('get')
            ->with(20, $data)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->get(20);
    }

    /**
     * Test getList
     *  Empty result
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetListEmptyResult()
    {
        $data = array('foo' => 'bar');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('getList'));

        $mockService->expects($this->once())
            ->method('getList')
            ->with($data)
            ->will($this->returnValue(null));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        // Still get 200 as the search was successful
        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200);

        $this->controller->getList();
    }

    /**
     * Test getList
     *  With Result
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetListWithResult()
    {
        $data = array('foo' => 'bar');

        $result = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('getList'));

        $mockService->expects($this->once())
            ->method('getList')
            ->with($data)
            ->will($this->returnValue($result));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, 'Results found', $result);

        $this->controller->getList();
    }

    /**
     * Test getList
     *  Throws Exception
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testGetListThrowsException()
    {
        $data = array('foo' => 'bar');

        $this->getMockController(array('checkMethod', 'getService', 'respond', 'getDataFromQuery'));

        $mockService = $this->getMock('\stdClass', array('getList'));

        $mockService->expects($this->once())
            ->method('getList')
            ->with($data)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($data));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->getList();
    }

    /**
     * Test update
     *  Invalid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateInvalidData()
    {
        $this->getMockController(array('checkMethod', 'formatDataFromJson'));

        $responseMock = $this->getMock('\Zend\Http\Response');

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($responseMock));

        $this->assertEquals($responseMock, $this->controller->update(1, array('foo' => 'bar')));
    }

    /**
     * Test patch
     *  Invalid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchInvalidData()
    {
        $this->getMockController(array('checkMethod', 'formatDataFromJson'));

        $responseMock = $this->getMock('\Zend\Http\Response');

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($responseMock));

        $this->assertEquals($responseMock, $this->controller->patch(1, array('foo' => 'bar')));
    }

    /**
     * Test update
     *  Valid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateValidData()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('update'));

        $serviceMock->expects($this->once())
            ->method('update')
            ->with($id, $formattedDate)
            ->will($this->returnValue(true));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200);

        $this->controller->update($id, $data);
    }

    /**
     * Test patch
     *  Valid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchValidData()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('patch'));

        $serviceMock->expects($this->once())
            ->method('patch')
            ->with($id, $formattedDate)
            ->will($this->returnValue(true));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200);

        $this->controller->patch($id, $data);
    }

    /**
     * Test update
     *  Valid data
     *  Failed save
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateValidDataFailedSave()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('update'));

        $serviceMock->expects($this->once())
            ->method('update')
            ->with($id, $formattedDate)
            ->will($this->returnValue(false));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->update($id, $data);
    }

    /**
     * Test patch
     *  Valid data
     *  Failed save
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchValidDataFailedSave()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('patch'));

        $serviceMock->expects($this->once())
            ->method('patch')
            ->with($id, $formattedDate)
            ->will($this->returnValue(false));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->patch($id, $data);
    }

    /**
     * Test update
     *  Valid data
     *  No Version
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateValidDataNoVersion()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('update'));

        $serviceMock->expects($this->once())
            ->method('update')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Olcs\Db\Exceptions\NoVersionException));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_400);

        $this->controller->update($id, $data);
    }

    /**
     * Test patch
     *  Valid data
     *  No Version
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchValidDataNoVersion()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('patch'));

        $serviceMock->expects($this->once())
            ->method('patch')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Olcs\Db\Exceptions\NoVersionException));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_400);

        $this->controller->patch($id, $data);
    }

    /**
     * Test update
     *  Valid data
     *  OptimisticLocking
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateValidDataOptimisticLocking()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $optimisticLockingException = $this->getMockBuilder('\Doctrine\ORM\OptimisticLockException')->disableOriginalConstructor()->getMock();

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('update', 'get'));

        $serviceMock->expects($this->once())
            ->method('update')
            ->with($id, $formattedDate)
            ->will($this->throwException($optimisticLockingException));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_409);

        $this->controller->update($id, $data);
    }

    /**
     * Test patch
     *  Valid data
     *  OptimisticLocking
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchValidDataOptimisticLocking()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $optimisticLockingException = $this->getMockBuilder('\Doctrine\ORM\OptimisticLockException')->disableOriginalConstructor()->getMock();

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('patch', 'get'));

        $serviceMock->expects($this->once())
            ->method('patch')
            ->with($id, $formattedDate)
            ->will($this->throwException($optimisticLockingException));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_409);

        $this->controller->patch($id, $data);
    }

    /**
     * Test update
     *  Valid data
     *  Unknown error
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateValidDataUnknownError()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('update'));

        $serviceMock->expects($this->once())
            ->method('update')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->update($id, $data);
    }

    /**
     * Test patch
     *  Valid data
     *  Unknown error
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testPatchValidDataUnknownError()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');

        $this->getMockController(array('checkMethod', 'formatDataFromJson', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('patch'));

        $serviceMock->expects($this->once())
            ->method('patch')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->patch($id, $data);
    }

    /**
     * Test delete
     *  Successful
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testDeleteSuccessful()
    {
        $id = 1;

        $this->getMockController(array('checkMethod', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('delete'));

        $serviceMock->expects($this->once())
            ->method('delete')
            ->with($id)
            ->will($this->returnValue(true));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200);

        $this->controller->delete($id);
    }

    /**
     * Test delete
     *  Failed
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testDeleteFailed()
    {
        $id = 1;

        $this->getMockController(array('checkMethod', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('delete'));

        $serviceMock->expects($this->once())
            ->method('delete')
            ->with($id)
            ->will($this->returnValue(false));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->delete($id);
    }

    /**
     * Test delete
     *  Unknown Error
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testDeleteUnknownError()
    {
        $id = 1;

        $this->getMockController(array('checkMethod', 'getService', 'respond'));

        $serviceMock = $this->getMock('\stdClass', array('delete'));

        $serviceMock->expects($this->once())
            ->method('delete')
            ->with($id)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('checkMethod');

        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_500);

        $this->controller->delete($id);
    }

    /**
     * Test serviceExists with missing service
     */
    public function testServiceExistsWithoutService()
    {
        $this->getMockController();

        $this->assertFalse($this->controller->serviceExists('MISSING'));
    }

    /**
     * Test serviceExists with service
     */
    public function testServiceExistsWithService()
    {
        $this->getMockController();

        $this->assertTrue($this->controller->serviceExists('Generic'));
    }

    /**
     * Test getService with name that exists
     */
    public function testGetServiceWithName()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Bob')
            ->will($this->returnValue('Service'));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(true));

        $this->assertEquals('Service', $this->controller->getService('Bob'));
    }

    /**
     * Test getService with name that doesn't exists
     */
    public function testGetServiceWithNameThatDoesntExist()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists'));

        $serviceMock = $this->getMock('\stdClass', array('setEntityName'));

        $serviceMock->expects($this->once())
            ->method('setEntityName')
            ->with('\OlcsEntities\Entity\Bob')
            ->will($this->returnValue('Service'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Generic')
            ->will($this->returnValue($serviceMock));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(false));

        $this->assertEquals('Service', $this->controller->getService('Bob'));
    }

    /**
     * Test getService without name
     */
    public function testGetServiceWithoutNameWithSetServiceName()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Bob')
            ->will($this->returnValue('Service'));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(true));

        $this->controller->setServiceName('Bob');

        $this->assertEquals('Service', $this->controller->getService());
    }

    /**
     * Test getService with name that doesn't exists, setServiceName
     */
    public function testGetServiceWithNameThatDoesntExistWithSetServiceName()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists'));

        $serviceMock = $this->getMock('\stdClass', array('setEntityName'));

        $serviceMock->expects($this->once())
            ->method('setEntityName')
            ->with('\OlcsEntities\Entity\Bob')
            ->will($this->returnValue('Service'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Generic')
            ->will($this->returnValue($serviceMock));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(false));

        $this->controller->setServiceName('Bob');

        $this->assertEquals('Service', $this->controller->getService());
    }

    /**
     * Test getService without name, with getControllerName
     */
    public function testGetServiceWithoutNameWithGetControllerName()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists', 'getControllerName'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Bob')
            ->will($this->returnValue('Service'));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(true));

        $this->controller->expects($this->once())
            ->method('getControllerName')
            ->will($this->returnValue('Bob'));

        $this->assertEquals('Service', $this->controller->getService());
    }

    /**
     * Test getService with name that doesn't exists, getControllerName
     */
    public function testGetServiceWithNameThatDoesntExistWithGetControllerName()
    {
        $this->getMockController(array('getServiceLocator', 'serviceExists', 'getControllerName'));

        $serviceMock = $this->getMock('\stdClass', array('setEntityName'));

        $serviceMock->expects($this->once())
            ->method('setEntityName')
            ->with('\OlcsEntities\Entity\Bob')
            ->will($this->returnValue('Service'));

        $serviceFactoryMock = $this->getMock('\stdClass', array('getService'));

        $serviceFactoryMock->expects($this->once())
            ->method('getService')
            ->with('Generic')
            ->will($this->returnValue($serviceMock));

        $serviceLocatorMock = $this->getMock('\stdClass', array('get'));

        $serviceLocatorMock->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($serviceFactoryMock));

        $this->controller->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocatorMock));

        $this->controller->expects($this->once())
            ->method('serviceExists')
            ->with('Bob')
            ->will($this->returnValue(false));

        $this->controller->expects($this->once())
            ->method('getControllerName')
            ->will($this->returnValue('Bob'));

        $this->assertEquals('Service', $this->controller->getService());
    }
}
