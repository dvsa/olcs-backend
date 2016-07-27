<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\Tm\UpdateNysiisName;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;
use Dvsa\Olcs\Api\Domain\Repository\ContactDetails as ContactDetailsRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Tm\UpdateNysiisName as Cmd;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Service\Data\Nysiis as NysiisService;
use ZfcRbac\Service\AuthorizationService;

/**
 * Transport Manager / Update NYSIIS Name
 *
 * @author Shaun Lizzio <shaun@lizzzio.co.uk>
 */
class UpdateNysiisNameTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateNysiisName();

        $this->mockRepo('TransportManager', TransportManagerRepo::class);
        $this->mockRepo('ContactDetails', ContactDetailsRepo::class);

        $this->mockedSmServices[NysiisService::class] = m::mock(NysiisService::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerEntity::TRANSPORT_MANAGER_STATUS_CURRENT,
            TransportManagerEntity::TRANSPORT_MANAGER_TYPE_BOTH
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 1;
        $data = [
            'id' => $id
        ];

        $command = Cmd::create($data);

        $person = new PersonEntity();
        $person->setForename('fn');
        $person->setFamilyName('ln');

        $transportManager = new TransportManagerEntity();
        $transportManager->setHomeCd(
            new ContactDetailsEntity(
                m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
            )
        );
        $transportManager->getHomeCd()->setPerson($person);

        $this->mockedSmServices[NysiisService::class]
            ->shouldReceive('getNysiisSearchKeys')
            ->once()
            ->andReturn(
                [
                    'nysiisForename' => 'nysiis fn',
                    'nysiisFamilyname' => 'nysiis ln'
                ]
            );

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($transportManager)
            ->once()
            ->shouldReceive('save')
            ->with($transportManager)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals('TM NYIIS name was requested and updated', $result->getMessages()[0]);
        $this->assertEquals('fn', $transportManager->getHomeCd()->getPerson()->getForename());
        $this->assertEquals('nysiis fn', $transportManager->getNysiisForename());
        $this->assertEquals('ln', $transportManager->getHomeCd()->getPerson()->getFamilyName());
        $this->assertEquals('nysiis ln', $transportManager->getNysiisFamilyName());
    }
}
