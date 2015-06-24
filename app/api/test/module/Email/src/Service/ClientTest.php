<?php

namespace Dvsa\OlcsTest\Email\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Service\Client;

/**
 * ClientTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ClientTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Client();
    }

    public function testSendEmailNoBody()
    {
        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');

        $this->setExpectedException(\RuntimeException::class);

        $this->sut->sendEmail($message);
    }

    public function testSendEmail()
    {
        $this->sut->setBaseUri('BASE_URI');
        $this->sut->setSelfServeUri('SELFSERVE_URI');

        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setFromEmail('FROM_EMAIL')
            ->setFromName('FROM_NAME')
            ->setBody('BODY')
            ->setHtml(false)
            ->setSubject('SUBJECT')
            ->setLocale('LO');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockPost = m::mock(\Zend\Stdlib\ParametersInterface::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockTranslator = m::mock(\Zend\I18n\Translator\Translator::class);

        $this->sut->setHttpClient($mockHttpClient);
        $this->sut->setTranslator($mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('SUBJECT', 'email', 'LO')->once()->andReturn('T_SUBJECT');
        $mockTranslator->shouldReceive('translate')->with('BODY', 'email', 'LO')->once()
            ->andReturn('T_BODY http://selfserve/foobar');

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->with('BASE_URI')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('POST')->once()->andReturnSelf();
        $mockRequest->shouldReceive('getPost')->once()->andReturn($mockPost);
        $mockPost->shouldReceive('set')->with('to', 'TO')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('subject', 'T_SUBJECT')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('body', 'T_BODY SELFSERVE_URI/foobar')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('html', false)->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromEmail', 'FROM_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromName', 'FROM_NAME')->once()->andReturnSelf();

        $response = new \Zend\Http\Response();
        $response->setStatusCode(202);

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->assertTrue($this->sut->sendEmail($message));
    }

    public function testSendEmailDeafultFrom()
    {
        $this->sut->setDefaultFrom('FROM_EMAIL', 'FROM_NAME');

        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setBody('BODY')
            ->setHtml(false)
            ->setSubject('SUBJECT')
            ->setLocale('LO');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockPost = m::mock(\Zend\Stdlib\ParametersInterface::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockTranslator = m::mock(\Zend\I18n\Translator\Translator::class);

        $this->sut->setHttpClient($mockHttpClient);
        $this->sut->setTranslator($mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('SUBJECT', 'email', 'LO')->once()->andReturn('T_SUBJECT');
        $mockTranslator->shouldReceive('translate')->with('BODY', 'email', 'LO')->once()->andReturn('T_BODY');

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('POST')->once()->andReturnSelf();
        $mockRequest->shouldReceive('getPost')->once()->andReturn($mockPost);
        $mockPost->shouldReceive('set')->with('to', 'TO')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('subject', 'T_SUBJECT')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('body', 'T_BODY')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('html', false)->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromEmail', 'FROM_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromName', 'FROM_NAME')->once()->andReturnSelf();

        $response = new \Zend\Http\Response();
        $response->setStatusCode(202);

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->assertTrue($this->sut->sendEmail($message));
    }

    public function testSendEmailSendAllEmailTo()
    {
        $this->sut->setDefaultFrom('FROM_EMAIL', 'FROM_NAME');
        $this->sut->setSendAllMailTo('ALL_EMAIL');

        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setBody('BODY')
            ->setHtml(false)
            ->setSubject('SUBJECT')
            ->setLocale('LO');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockPost = m::mock(\Zend\Stdlib\ParametersInterface::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);

        $this->sut->setHttpClient($mockHttpClient);

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('POST')->once()->andReturnSelf();
        $mockRequest->shouldReceive('getPost')->once()->andReturn($mockPost);
        $mockPost->shouldReceive('set')->with('to', 'ALL_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('subject', 'TO : SUBJECT')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('body', 'BODY')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('html', false)->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromEmail', 'FROM_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromName', 'FROM_NAME')->once()->andReturnSelf();

        $response = new \Zend\Http\Response();
        $response->setStatusCode(202);

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->assertTrue($this->sut->sendEmail($message));
    }

    public function testSendFailsUnknownError()
    {
        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setFromEmail('FROM_EMAIL')
            ->setFromName('FROM_NAME')
            ->setBody('BODY')
            ->setHtml(false)
            ->setSubject('SUBJECT')
            ->setLocale('LO');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockPost = m::mock(\Zend\Stdlib\ParametersInterface::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockTranslator = m::mock(\Zend\I18n\Translator\Translator::class);

        $this->sut->setHttpClient($mockHttpClient);
        $this->sut->setTranslator($mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('SUBJECT', 'email', 'LO')->once()->andReturn('T_SUBJECT');
        $mockTranslator->shouldReceive('translate')->with('BODY', 'email', 'LO')->once()->andReturn('T_BODY');

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('POST')->once()->andReturnSelf();
        $mockRequest->shouldReceive('getPost')->once()->andReturn($mockPost);
        $mockPost->shouldReceive('set')->with('to', 'TO')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('subject', 'T_SUBJECT')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('body', 'T_BODY')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('html', false)->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromEmail', 'FROM_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromName', 'FROM_NAME')->once()->andReturnSelf();

        $response = new \Zend\Http\Response();
        $response->setStatusCode(200);

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->setExpectedException(
            \Dvsa\Olcs\Email\Exception\EmailNotSentException::class,
            'Unknown error sending email'
        );

        $this->assertTrue($this->sut->sendEmail($message));
    }

    public function testSendFails()
    {
        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setFromEmail('FROM_EMAIL')
            ->setFromName('FROM_NAME')
            ->setBody('BODY')
            ->setHtml(false)
            ->setSubject('SUBJECT')
            ->setLocale('LO');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockPost = m::mock(\Zend\Stdlib\ParametersInterface::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockTranslator = m::mock(\Zend\I18n\Translator\Translator::class);

        $this->sut->setHttpClient($mockHttpClient);
        $this->sut->setTranslator($mockTranslator);

        $mockTranslator->shouldReceive('translate')->with('SUBJECT', 'email', 'LO')->once()->andReturn('T_SUBJECT');
        $mockTranslator->shouldReceive('translate')->with('BODY', 'email', 'LO')->once()->andReturn('T_BODY');

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('POST')->once()->andReturnSelf();
        $mockRequest->shouldReceive('getPost')->once()->andReturn($mockPost);
        $mockPost->shouldReceive('set')->with('to', 'TO')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('subject', 'T_SUBJECT')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('body', 'T_BODY')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('html', false)->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromEmail', 'FROM_EMAIL')->once()->andReturnSelf();
        $mockPost->shouldReceive('set')->with('fromName', 'FROM_NAME')->once()->andReturnSelf();

        $response = m::mock(\Zend\Http\Response::class);
        $response->shouldReceive('getStatusCode')->with()->once()->andReturn(200);
        $response->shouldReceive('getBody')->with()->once()->andReturn('{"errorMessage" : "ERROR_1"}');

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->setExpectedException(
            \Dvsa\Olcs\Email\Exception\EmailNotSentException::class,
            'ERROR_1'
        );

        $this->assertTrue($this->sut->sendEmail($message));
    }
}
