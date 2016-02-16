<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\System;

use Dvsa\Olcs\Api\Domain\CommandHandler\System\UpdateSlaTargetDate as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\SlaTargetDate as Repo;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate as Entity;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate;
use Dvsa\Olcs\Transfer\Command\System\UpdateSlaTargetDate as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * UpdateSlaTargetDate command handler test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateSlaTargetDateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('SlaTargetDate', Repo::class);
        $this->mockRepo('User', UserRepo::class);
        $this->mockRepo('document', DocumentRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Entity::class => [
                99 => m::mock(Entity::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $params = [
            'agreedDate' => '2015-09-10',
            'sentDate' => '2015-09-10',
            'underDelegation' => 'Y',
            'targetDate' => '2015-09-10',
            'notes' => 'test notes',
            'entityType' => 'document',
            'entityId' => 100,
            'version' => 33

        ];
        $command = Command::create($params);

        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        $this->mockedSmServices['ZfcRbac\Service\AuthorizationService']
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        /** @var Entity $slaTargetDateEntity */
        $slaTargetDateEntity = $this->getSlaTargetDateEntity();

        $this->repoMap['SlaTargetDate']->shouldReceive('fetchUsingEntityIdAndType')
            ->once()
            ->with($params['entityType'], $params['entityId'], $params['version'])
            ->andReturn($slaTargetDateEntity)
            ->shouldReceive('save');

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['SlaTargetDate Updated successfully'], $result->getMessages());
        $this->assertEquals($params['agreedDate'], $slaTargetDateEntity->getAgreedDate());
        $this->assertEquals($params['sentDate'], $slaTargetDateEntity->getSentDate());
        $this->assertEquals($params['targetDate'], $slaTargetDateEntity->getTargetDate());
        $this->assertEquals($params['underDelegation'], $slaTargetDateEntity->getUnderDelegation());
        $this->assertEquals($params['notes'], $slaTargetDateEntity->getNotes());
    }

    public function testHandleCommandInvalidEntityType()
    {
        $params = [
            'agreedDate' => '2015-09-10',
            'sentDate' => '2015-09-10',
            'underDelegation' => 'Y',
            'targetDate' => '2015-09-10',
            'notes' => 'test notes',
            'entityType' => 'notsupported',
            'entityId' => 100,
            'version' => 33

        ];
        $command = Command::create($params);

        $this->setExpectedException(ValidationException::class);
        $this->sut->handleCommand($command);
    }

    private function getSlaTargetDateEntity()
    {
        $entity = m::mock(SlaTargetDate::class)->makePartial();

        $entity->setAgreedDate('2000-01-01');
        $entity->setSentDate('2000-01-02');
        $entity->setTargetDate('2000-01-03');
        $entity->setUnderDelegation('N');
        $entity->setNotes('Notes');

        return $entity;
    }
}
