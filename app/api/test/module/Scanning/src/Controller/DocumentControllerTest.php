<?php

/**
 * Document controller test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Scanning\Controller;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Olcs\Logging\Log\Logger;

/**
 * Document controller test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentControllerTest extends MockeryTestCase
{
    protected $sut;

    /**
     * @var \Zend\Log\Writer\Mock
     */
    protected $logWriter;

    private $mockCommandHandlerManager;

    protected function setUp()
    {
        $this->sm = \OlcsTest\Bootstrap::getServiceManager();
        $this->request  = m::mock('\Zend\Http\Request')->makePartial();
        $this->response = m::mock('\Zend\Http\Response')->makePartial();

        $this->sut = m::mock(\Dvsa\Olcs\Scanning\Controller\DocumentController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($this->request);

        // Mock the logger
        $this->logWriter = new \Zend\Log\Writer\Mock();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($this->logWriter);
        Logger::setLogger($logger);

        $this->mockCommandHandlerManager = m::mock();
        $this->sm->setService('CommandHandlerManager', $this->mockCommandHandlerManager);

        $this->sut->setServiceLocator($this->sm);
    }

    public function testCreateWithInvalidRequest()
    {
        $scanMock = m::mock()
            ->shouldReceive('setDataFromRequest')
            ->with($this->request)
            ->shouldReceive('isValidRequest')
            ->andReturn(false)
            ->getMock();

        $this->sm->setService('Scanning', $scanMock);

        $this->sut->shouldReceive('respondError')
            ->once()
            ->with(400, 'Bad Request');

        $this->sut->create([]);
    }

    public function testCreateWithValidRequestButInvalidMimeType()
    {
        $scanMock = m::mock()
            ->shouldReceive('setDataFromRequest')
            ->with($this->request)
            ->shouldReceive('isValidRequest')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(
                [
                    'image' => ['tmp_name' => 'file-data', 'name' => 'foo'],
                    'description' => 34
                ]
            )
            ->getMock();

        $this->sm->setService('Scanning', $scanMock);

        $this->sut->shouldReceive('getUploadContents')->andReturn('CONTENTS');

        $e = new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(['SCAN_INVALID_MIME' => 'foo']);
        $this->mockCommandHandlerManager->shouldReceive('handleCommand')->andThrow($e);

        $this->sut->shouldReceive('respondError')
            ->once()
            ->with(415, 'Unsupported Media Type');

        $this->sut->create([]);
    }

    public function testCreateWithValidRequestButScanNotFound()
    {
        $scanMock = m::mock()
            ->shouldReceive('setDataFromRequest')
            ->with($this->request)
            ->shouldReceive('isValidRequest')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(
                [
                    'image' => ['tmp_name' => 'file-data', 'name' => 'foo'],
                    'description' => 34
                ]
            )
            ->getMock();

        $this->sm->setService('Scanning', $scanMock);

        $this->sut->shouldReceive('getUploadContents')->andReturn('CONTENTS');

        $e = new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(['SCAN_NOT_FOUND' => 'foo']);
        $this->mockCommandHandlerManager->shouldReceive('handleCommand')->andThrow($e);

        $this->sut->shouldReceive('respondError')
            ->once()
            ->with(400, 'Cannot find scan record');

        $this->sut->create([]);
    }

    public function testCreateWithValidRequestButOtherError()
    {
        $scanMock = m::mock()
            ->shouldReceive('setDataFromRequest')
            ->with($this->request)
            ->shouldReceive('isValidRequest')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(
                [
                    'image' => ['tmp_name' => 'file-data', 'name' => 'foo'],
                    'description' => 34
                ]
            )
            ->getMock();

        $this->sm->setService('Scanning', $scanMock);

        $this->sut->shouldReceive('getUploadContents')->andReturn('CONTENTS');

        $e = new \Exception('an error');
        $this->mockCommandHandlerManager->shouldReceive('handleCommand')->andThrow($e);

        $this->sut->shouldReceive('respondError')
            ->once()
            ->with(500, 'Internal Server Error');

        $this->sut->create([]);
    }

    public function testCreateWithValidRequest()
    {
        $scanMock = m::mock()
            ->shouldReceive('setDataFromRequest')
            ->with($this->request)
            ->shouldReceive('isValidRequest')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(
                [
                    'image' => ['tmp_name' => 'file-data', 'name' => 'foo'],
                    'description' => 34
                ]
            )
            ->getMock();

        $this->sm->setService('Scanning', $scanMock);

        $this->sut->shouldReceive('getUploadContents')->andReturn('CONTENTS');

        $this->mockCommandHandlerManager->shouldReceive('handleCommand');

        $response = $this->sut->create([]);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('', $response->getContent());
    }
}
