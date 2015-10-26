<?php
namespace Olcs\Email\Controller;

use Olcs\Email\Controller\EmailController;
use Olcs\Email\Service\Email as EmailService;
use Zend\Mail\Transport\Null as NullTransport;
use Zend\Http\Response;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * EmailControllerTest
 */
class EmailControllerTest extends TestCase
{
    private $mailService;
    private $controller;

    protected function setUp()
    {
        $this->mailService = m::mock('Olcs\Email\Service\Email');
        $this->mailService->shouldReceive('send')->andReturn('MY-DATA');

        $sl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $sl->shouldReceive('get')->with('Email')->andReturn($this->mailService);
        $sl->shouldReceive('getServiceLocator')->andReturnSelf();

        $this->controller = new EmailController();
        $this->controller->createService($sl);
    }

    public function testCreate()
    {
        $data = [];
        $data['fromEmail'] = 'some@email.com';
        $data['fromName'] = 'Fred Smith';
        $data['to'] = 'to@some-email.com';
        $data['subject'] = 'Some Subject';
        $data['body'] = 'Some body';
        $data['html'] = '1';

        $response = $this->controller->create($data);

        $this->assertEquals(Response::STATUS_CODE_202, $response->getStatusCode());

        $this->assertEquals(
            '{"Response":{"Code":202,"Message":"Accepted","Summary":"Complete","Data":"MY-DATA"}}',
            $response->getContent()
        );
    }

    public function testGetList()
    {
        $response = $this->controller->getList();

        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());

        $this->assertEquals(
            '{"Response":{"Code":200,"Message":"OK","Summary":"Service is online.","Data":""}}',
            $response->getContent()
        );
    }
}
