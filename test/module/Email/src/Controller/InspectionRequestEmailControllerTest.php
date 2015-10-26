<?php
namespace Olcs\Email\Controller;

use Olcs\Email\Controller\InspectionRequestEmailController;
use Olcs\Email\Service\Email as EmailService;
use Zend\Mail\Transport\Null as NullTransport;
use Zend\Http\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * InspectionRequestEmailControllerTest
 */
class InspectionRequestEmailControllerTest extends TestCase
{
    private $imapService;
    private $controller;

    protected function setUp()
    {
        $this->imapService = m::mock('Olcs\Email\Service\Imap');

        $sl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $sl->shouldReceive('get')->with('Imap')->andReturn($this->imapService);
        $sl->shouldReceive('getServiceLocator')->andReturnSelf();

        $this->controller = new InspectionRequestEmailController();
        $this->controller->createService($sl);
    }

    public function testGet()
    {
        $id = 99;
        $message = ['MESSAGE-DATA'];

        $this->imapService
            ->shouldReceive('connect')
            ->with('inspection_request')
            ->andReturnSelf()
            ->shouldReceive('getMessage')
            ->with($id)
            ->once()
            ->andReturn($message);

        $response = $this->controller->get($id);

        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Success","Data":["MESSAGE-DATA"]}}',
            $response->getContent()
        );
    }

    public function testGetList()
    {
        $messages = ['MESSAGE-DATA'];

        $this->imapService
            ->shouldReceive('connect')
            ->with('inspection_request')
            ->andReturnSelf()
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn($messages);

        $response = $this->controller->getList();

        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Success","Data":["MESSAGE-DATA"]}}',
            $response->getContent()
        );
    }

    public function testDelete()
    {
        $id = 99;

        $this->imapService
            ->shouldReceive('connect')
            ->with('inspection_request')
            ->andReturnSelf()
            ->shouldReceive('removeMessage')
            ->with($id)
            ->once();

        $response = $this->controller->delete($id);

        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Success","Data":null}}',
            $response->getContent()
        );
    }
}
