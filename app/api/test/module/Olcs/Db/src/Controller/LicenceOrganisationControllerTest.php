<?php

/**
 * Tests LicenceOrganisationController
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\LicenceVehicleController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests LicenceVehicleController
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class LicenceOrganisationControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\LicenceOrganisationController',
            array(
                'respond',
                'getDataFromQuery',
                'getService',
                'checkMethod',
                'formatDataFromJson',
            )
        );
    }

/**
     * Test get
     *  Empty result
     *
     * @group Controller
     */
    public function testGetEmptyResult()
    {
        $mockService = $this->getMock('\stdClass', array('getByLicenceId'));

        $mockService->expects($this->once())
            ->method('getByLicenceId')
            ->with(20)
            ->will($this->returnValue(null));

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
     *  Test get
     *  With Result
     *
     * @group Controller
     */
    public function testGetWithResult()
    {
       $mockService = $this->getMock('\stdClass', array('getByLicenceId'));
    
        $mockService->expects($this->once())
            ->method('getByLicenceId')
            ->with(20)
            ->will($this->returnValue(array('foo' => 'bar')));
    
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
     */
    public function testGetThrowsException()
    {
        
        $mockService = $this->getMock('\stdClass', array('getByLicenceId'));
    
        $mockService->expects($this->once())
            ->method('getByLicenceId')
            ->with(20)
            ->will($this->throwException(new \Exception));
    
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
     * Test update
     *  Invalid data
     *
     * @group Controller
     * @group AbstractBasicRestServerController
     */
    public function testUpdateInvalidData()
    {
        $responseMock = $this->getMock('\Zend\Http\Response');
    
        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($responseMock));
        
        $class = new \ReflectionClass('\Olcs\Db\Controller\LicenceOrganisationController');
        $method = $class->getMethod('updateOrPatch');
        $method->setAccessible(true);
    
        $this->assertEquals($responseMock, $method->invokeArgs($this->controller, array(1, array('foo' => 'bar'), 'UPDATE')));
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
    
        $serviceMock = $this->getMock('\stdClass', array('updateByLicenceId'));
    
        $serviceMock->expects($this->once())
            ->method('updateByLicenceId')
            ->with($id, $formattedDate)
            ->will($this->returnValue(true));
    
        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($formattedDate));
    
        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($serviceMock));
    
        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200);
        
        $class = new \ReflectionClass('\Olcs\Db\Controller\LicenceOrganisationController');
        $method = $class->getMethod('updateOrPatch');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, array($id, $data, 'UPDATE'));
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
    
        $serviceMock = $this->getMock('\stdClass', array('updateByLicenceId'));
    
        $serviceMock->expects($this->once())
            ->method('updateByLicenceId')
            ->with($id, $formattedDate)
            ->will($this->returnValue(false));
    
    
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
     * Test update
     *  Valid data
     *  No Version
     *
     * @group Controller
     */
    public function testUpdateValidDataNoVersion()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');
    
        
        $serviceMock = $this->getMock('\stdClass', array('updateByLicenceId'));
    
        $serviceMock->expects($this->once())
            ->method('updateByLicenceId')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Olcs\Db\Exceptions\NoVersionException));
    
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
     * Test update
     *  Valid data
     *  OptimisticLocking
     *
     * @group Controller
     */
    public function testUpdateValidDataOptimisticLocking()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');
    
        $optimisticLockingException = $this->getMockBuilder('\Doctrine\ORM\OptimisticLockException')->disableOriginalConstructor()->getMock();
    
        $serviceMock = $this->getMock('\stdClass', array('update', 'updateByLicenceId', 'getByLicenceId'));
    
        $serviceMock->expects($this->once())
            ->method('updateByLicenceId')
            ->with($id, $formattedDate)
            ->will($this->throwException($optimisticLockingException));
    
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
     * Test update
     *  Valid data
     *  Unknown error
     *
     * @group Controller
     */
    public function testUpdateValidDataUnknownError()
    {
        $id = 1;
        $data = array('foo' => 'bar');
        $formattedDate = array('foo' => 'cake');
    
        $serviceMock = $this->getMock('\stdClass', array('updateByLicenceId'));
    
        $serviceMock->expects($this->once())
            ->method('updateByLicenceId')
            ->with($id, $formattedDate)
            ->will($this->throwException(new \Exception));
    
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
    
    
}
