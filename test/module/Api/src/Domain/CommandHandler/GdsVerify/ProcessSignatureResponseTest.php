<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\GdsVerify\ProcessSignatureResponse;
use Dvsa\Olcs\Api\Domain\Repository\DiscSequened as DiscSequenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as GoodsDiscRepo;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\GdsVerify\ProcessSignatureResponse as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use \Dvsa\Olcs\GdsVerify\Service;

/**
 * ProcessSignatureResponseTest
 */
class ProcessSignatureResponseTest extends CommandHandlerTestCase
{
    /**
     * @var ProcessSignatureResponse
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new ProcessSignatureResponse();
        $this->mockRepo('DigitalSignature', \Dvsa\Olcs\Api\Domain\Repository\DigitalSignature::class);
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        $this->refData = [];

        $this->mockedSmServices[Service\GdsVerify::class] = m::mock(Service\GdsVerify::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            \Dvsa\Olcs\Api\Entity\Application\Application::SIG_DIGITAL_SIGNATURE,
        ];

        parent::initReferences();
    }

    public function testHandleCommandInvalidSignature()
    {
        $command = Cmd::create(['samlResponse' => 'SAML']);

        $attributes = m::mock(\Dvsa\Olcs\GdsVerify\Data\Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(false);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with('SAML')->once()->andReturn($attributes);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['samlResponse' => base64_encode('SAML'), 'application' => 7]);

        $attributes = m::mock(\Dvsa\Olcs\GdsVerify\Data\Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(true);
        $attributes->shouldReceive('getArrayCopy')->with()->once()->andReturn(['foo' => 'bar']);
        $this->mockedSmServices[Service\GdsVerify::class]
            ->shouldReceive('getAttributesFromResponse')->with(base64_encode('SAML'))->once()->andReturn($attributes);

        $this->repoMap['DigitalSignature']->shouldReceive('save')->once()->andReturnUsing(
            function ($digitalSignature) {
                /** @var \Dvsa\Olcs\Api\Entity\DigitalSignature $digitalSignature */
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class);
        $mockApplication->shouldReceive('setDigitalSignature')->once()->andReturnUsing(
            function ($digitalSignature) {
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );
        $mockApplication->shouldReceive('setSignatureType')
            ->with($this->refData[\Dvsa\Olcs\Api\Entity\Application\Application::SIG_DIGITAL_SIGNATURE])
            ->once();

        $this->repoMap['Application']->shouldReceive('fetchById')->with(7)->once()->andReturn($mockApplication);
        $this->repoMap['Application']->shouldReceive('save')->once();

        $this->sut->handleCommand($command);
    }

    public function testSetGetGdsVerifyService()
    {
        $sut = new ProcessSignatureResponse();
        $this->assertNull($sut->getGdsVerifyService());
        $mock = m::mock(Service\GdsVerify::class);
        $sut->setGdsVerifyService($mock);
        $this->assertSame($mock, $sut->getGdsVerifyService());
    }
}
