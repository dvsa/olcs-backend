<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\SendResponse;
use Dvsa\Olcs\Transfer\Command\Cases\Si\SendResponse as SendErruResponseCmd;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Service\Nr\MsiResponse as MsiResponseService;
use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Identity\IdentityInterface;
use Zend\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;

/**
 * SendResponseTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendResponseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SendResponse();
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('SeriousInfringement', SiRepo::class);

        $this->mockedSmServices = [
            MsiResponseService::class => m::mock(MsiResponseService::class),
            InrClientInterface::class => m::mock(InrClient::class),
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    /**
     * Tests sending the Msi response
     */
    public function testHandleCommand()
    {
        $responseDate = '2015-12-25 00:00:00';
        $xml = 'xml string';
        $userId = 111;
        $siId = 222;
        $caseId = 333;
        $command = SendErruResponseCmd::create(['case' => $caseId]);

        $user = m::mock(UserEntity::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $si = m::mock(SiEntity::class)->makePartial();
        $si->shouldReceive('getId')->once()->andReturn($siId);
        $si->shouldReceive('updateErruResponse')->once()->with(m::type(UserEntity::class), m::type(\DateTime::class));

        $case = m::mock(CasesEntity::class);
        $case->shouldReceive('getId')->once()->andReturn($caseId);
        $case->shouldReceive('getSeriousInfringements->first')->once()->andReturn($si);

        $this->repoMap['Cases']->shouldReceive('fetchById')->once()->with($caseId)->andReturn($case);
        $this->repoMap['SeriousInfringement']->shouldReceive('save')->once()->with(m::type(SiEntity::class));

        $rbacIdentity = m::mock(IdentityInterface::class);
        $rbacIdentity->shouldReceive('getUser')->andReturn($user);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($user);

        $this->mockedSmServices[MsiResponseService::class]
            ->shouldReceive('getResponseDateTime')
            ->once()
            ->andReturn($responseDate);

        $this->mockedSmServices[MsiResponseService::class]
            ->shouldReceive('create')
            ->once()
            ->with($case)
            ->andReturn($xml);

        $this->mockedSmServices[InrClientInterface::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->with($xml)
            ->andReturn(202);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'case' => $caseId,
                'serious_infringement' => $siId
            ],
            'messages' => [
                'Msi Response sent'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * Tests sending the Msi response
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testHandleCommandBadInrResponse()
    {
        $xml = 'xml string';
        $userId = 111;
        $caseId = 333;
        $case = m::mock(CasesEntity::class);
        $command = SendErruResponseCmd::create(['case' => $caseId]);

        $user = m::mock(UserEntity::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $this->repoMap['Cases']->shouldReceive('fetchById')->once()->with($caseId)->andReturn($case);

        $rbacIdentity = m::mock(IdentityInterface::class);
        $rbacIdentity->shouldReceive('getUser')->andReturn($user);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($user);

        $this->mockedSmServices[MsiResponseService::class]
            ->shouldReceive('create')
            ->once()
            ->with($case)
            ->andReturn($xml);

        $this->mockedSmServices[InrClientInterface::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->with($xml)
            ->andReturn(400);

        $this->sut->handleCommand($command);
    }

    /**
     * Tests sending the Msi response
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RestResponseException
     */
    public function testHandleCommandCurlException()
    {
        $xml = 'xml string';
        $userId = 111;
        $caseId = 333;
        $case = m::mock(CasesEntity::class);
        $command = SendErruResponseCmd::create(['case' => $caseId]);

        $user = m::mock(UserEntity::class);
        $user->shouldReceive('getId')->andReturn($userId);

        $this->repoMap['Cases']->shouldReceive('fetchById')->once()->with($caseId)->andReturn($case);

        $rbacIdentity = m::mock(IdentityInterface::class);
        $rbacIdentity->shouldReceive('getUser')->andReturn($user);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($user);

        $this->mockedSmServices[MsiResponseService::class]
            ->shouldReceive('create')
            ->once()
            ->with($case)
            ->andReturn($xml);

        $this->mockedSmServices[InrClientInterface::class]
            ->shouldReceive('makeRequest')
            ->once()
            ->with($xml)
            ->andThrow(AdapterRuntimeException::class);

        $this->sut->handleCommand($command);
    }
}
