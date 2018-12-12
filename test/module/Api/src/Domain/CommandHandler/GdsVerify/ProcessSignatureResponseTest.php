<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\GdsVerify;

use Dvsa\Olcs\Api\Domain\Command\Result;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\GdsVerify\Data\Attributes;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\GdsVerify\ProcessSignatureResponse;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\GdsVerify\ProcessSignatureResponse as Cmd;
use Dvsa\Olcs\GdsVerify\Service;
use Dvsa\Olcs\GdsVerify;

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
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('Surrender', \Dvsa\Olcs\Api\Domain\Repository\Surrender::class);
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);
        $this->mockRepo(
            'TransportManagerApplication',
            \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication::class
        );
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

        $attributes = m::mock(Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(false);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with('SAML')->once()->andReturn($attributes);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInvalidAssertion()
    {
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with('SAML')->once()->andThrow(GdsVerify\Exception::class, 'EXCEPTION_MESSAGE');

        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $command = Cmd::create(['samlResponse' => 'SAML']);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandApplication()
    {
        $command = Cmd::create(['samlResponse' => base64_encode('SAML'), 'application' => 7]);

        $attributes = m::mock(Attributes::class);
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

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class,
            ['id' => 7, 'section' => 'undertakings'],
            new Result()
        );

        $this->repoMap['Application']->shouldReceive('fetchById')->with(7)->once()->andReturn($mockApplication);
        $this->repoMap['Application']->shouldReceive('save')->once();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandContinuationDetail()
    {
        $command = Cmd::create(['samlResponse' => base64_encode('SAML'), 'continuationDetail' => 65]);

        $attributes = m::mock(Attributes::class);
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

        $mockContinuationDetail = m::mock(\Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail::class);
        $mockContinuationDetail->shouldReceive('setDigitalSignature')->once()->andReturnUsing(
            function ($digitalSignature) {
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );
        $mockContinuationDetail->shouldReceive('setSignatureType')
            ->with($this->refData[RefData::SIG_DIGITAL_SIGNATURE])
            ->once();
        $mockContinuationDetail->shouldReceive('setIsDigital')->with(true)->once();

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchById')->with(65)->once()
            ->andReturn($mockContinuationDetail);
        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandTransportManagerApplication()
    {

        $command = Cmd::create([
            'samlResponse' => base64_encode('SAML'),
            'transportManagerApplication' => 65,
            'role' => 'tma_sign_as_tm'
        ]);
        $attributes = m::mock(Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(true);
        $attributes->shouldReceive('getArrayCopy')->with()->once()->andReturn(['foo' => 'bar']);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with(base64_encode('SAML'))->once()->andReturn($attributes);

        $this->repoMap['DigitalSignature']->shouldReceive('save')->once()->andReturnUsing(
            function ($digitalSignature) {
                /** @var \Dvsa\Olcs\Api\Entity\DigitalSignature $digitalSignature */
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );


        $mockTransportApplication = m::mock(TransportManagerApplication::class);

        $mockTransportApplication->shouldReceive('setTmDigitalSignature')
            ->once();
        $mockTransportApplication->shouldReceive('setTmSignatureType')
            ->with($this->refData[RefData::SIG_DIGITAL_SIGNATURE])
            ->once();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchById')->with(65)->once()
            ->andReturn($mockTransportApplication);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Submit::class,
            ['id' => 65, 'nextStatus' => TransportManagerApplication::STATUS_TM_SIGNED],
            new Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testOperatorSignatureTransportManager()
    {
        $command = Cmd::create([
            'samlResponse' => base64_encode('SAML'),
            'transportManagerApplication' => 65,
            'role' => 'tma_sign_as_op'
        ]);
        $attributes = m::mock(Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(true);
        $attributes->shouldReceive('getArrayCopy')->with()->once()->andReturn(['foo' => 'bar']);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with(base64_encode('SAML'))->once()->andReturn($attributes);

        $this->repoMap['DigitalSignature']->shouldReceive('save')->once()->andReturnUsing(
            function ($digitalSignature) {
                /** @var \Dvsa\Olcs\Api\Entity\DigitalSignature $digitalSignature */
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );


        $mockTransportApplication = m::mock(TransportManagerApplication::class);

        $mockTransportApplication->shouldReceive('setOpDigitalSignature')
            ->once();
        $mockTransportApplication->shouldReceive('setOpSignatureType')
            ->once();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchById')->with(65)->once()
            ->andReturn($mockTransportApplication);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Submit::class,
            ['id' => 65, 'nextStatus' => TransportManagerApplication::STATUS_RECEIVED],
            new Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testOperatorTMSignatureTransportManager()
    {
        $command = Cmd::create([
            'samlResponse' => base64_encode('SAML'),
            'transportManagerApplication' => 65,
            'role' => 'tma_sign_as_top'
        ]);
        $attributes = m::mock(Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(true);
        $attributes->shouldReceive('getArrayCopy')->with()->once()->andReturn(['foo' => 'bar']);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with(base64_encode('SAML'))->once()->andReturn($attributes);

        $this->repoMap['DigitalSignature']->shouldReceive('save')->once()->andReturnUsing(
            function ($digitalSignature) {
                /** @var \Dvsa\Olcs\Api\Entity\DigitalSignature $digitalSignature */
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );


        $mockTransportApplication = m::mock(TransportManagerApplication::class);

        $mockTransportApplication->shouldReceive('setTmDigitalSignature')
            ->once();
        $mockTransportApplication->shouldReceive('setTmSignatureType')
            ->with($this->refData[RefData::SIG_DIGITAL_SIGNATURE])
            ->once();

        $mockTransportApplication->shouldReceive('setOpDigitalSignature')
            ->once();
        $mockTransportApplication->shouldReceive('setOpSignatureType')
            ->once();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchById')->with(65)->once()
            ->andReturn($mockTransportApplication);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Submit::class,
            ['id' => 65, 'nextStatus' => TransportManagerApplication::STATUS_RECEIVED],
            new Result()
        );

        $this->sut->handleCommand($command);
    }


    public function testProcessSignatureSurrender()
    {
        $data = [
            'samlResponse' => base64_encode('SAML'),
            'licence' => 65,
            'signatureType' => RefData::SIG_DIGITAL_SIGNATURE

        ];
        $command = Cmd::create($data);
        $attributes = m::mock(Attributes::class);
        $attributes->shouldReceive('isValidSignature')->with()->once()->andReturn(true);
        $attributes->shouldReceive('getArrayCopy')->with()->once()->andReturn(['foo' => 'bar']);
        $this->mockedSmServices[Service\GdsVerify::class]->shouldReceive('getAttributesFromResponse')
            ->with(base64_encode('SAML'))->once()->andReturn($attributes);

        $this->repoMap['DigitalSignature']->shouldReceive('save')->once()->andReturnUsing(
            function ($digitalSignature) {
                /** @var \Dvsa\Olcs\Api\Entity\DigitalSignature $digitalSignature */
                $this->assertSame(['foo' => 'bar'], $digitalSignature->getAttributesArray());
                $this->assertSame('SAML', $digitalSignature->getSamlResponse());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Surrender\Update::class,
            [
                'signatureType' => RefData::SIG_DIGITAL_SIGNATURE,
                'id' => 65,
                'status' => Surrender::SURRENDER_STATUS_SIGNED,
                'digitalSignature' => ''
            ],
            new Result()
        );

        $licence = m::mock(Licence::class)
            ->shouldReceive('setStatus')
            ->once()
            ->with(Licence::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION)
            ->getMock();



        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(65)
            ->once()
            ->andReturn($licence);

        $this->repoMap['Licence']
            ->shouldReceive('save')
            ->with($licence)
            ->once();

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
