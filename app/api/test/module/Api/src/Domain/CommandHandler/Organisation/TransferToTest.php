<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Organisation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\TransferTo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Organisation\TransferTo as Cmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * TransferToTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransferToTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TransferTo();
        $this->mockRepo('Organisation', \Dvsa\Olcs\Api\Domain\Repository\Organisation::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('IrfoGvPermit', \Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit::class);
        $this->mockRepo('IrfoPsvAuth', \Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth::class);
        $this->mockRepo('Task', \Dvsa\Olcs\Api\Domain\Repository\Task::class);
        $this->mockRepo('Disqualification', \Dvsa\Olcs\Api\Domain\Repository\Disqualification::class);
        $this->mockRepo('EbsrSubmission', \Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission::class);
        $this->mockRepo('TxcInbox', \Dvsa\Olcs\Api\Domain\Repository\TxcInbox::class);
        $this->mockRepo('EventHistory', \Dvsa\Olcs\Api\Domain\Repository\EventHistory::class);
        $this->mockRepo('OrganisationUser', \Dvsa\Olcs\Api\Domain\Repository\OrganisationUser::class);
        $this->mockRepo('Note', \Dvsa\Olcs\Api\Domain\Repository\Note::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        $this->references = [
        ];

        parent::initReferences();
    }

    public function testHandleCommandFromAndToSame()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($fromOrganisation);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\BadRequestException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithNoAssociatedEntities()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $result = $this->sut->handleCommand($command);

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandLicences()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $licence1= new Licence($fromOrganisation, new RefData());
        $fromOrganisation->addLicences($licence1);
        $licence2= new Licence($fromOrganisation, new RefData());
        $fromOrganisation->addLicences($licence2);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['Licence']->shouldReceive('save')->with($licence1)->once();
        $this->repoMap['Licence']->shouldReceive('save')->with($licence2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $licence1->getOrganisation());
        $this->assertSame($toOrganisation, $licence2->getOrganisation());

        $expectedResult = [
            '2 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandNotes()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $note1= new \Dvsa\Olcs\Api\Entity\Note\Note();
        $note1->setOrganisation($fromOrganisation);
        $note2= new \Dvsa\Olcs\Api\Entity\Note\Note();
        $note2->setOrganisation($fromOrganisation);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$note1, $note2]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['Note']->shouldReceive('save')->with($note1)->once();
        $this->repoMap['Note']->shouldReceive('save')->with($note2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $note1->getOrganisation());
        $this->assertSame($toOrganisation, $note2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '2 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandIrfoGvPermit()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $irfoGvPermit1= new \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit(
            $fromOrganisation,
            new \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType(),
            new RefData()
        );
        $irfoGvPermit2= new \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit(
            $fromOrganisation,
            new \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType(),
            new RefData()
        );

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$irfoGvPermit1, $irfoGvPermit2]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['IrfoGvPermit']->shouldReceive('save')->with($irfoGvPermit1)->once();
        $this->repoMap['IrfoGvPermit']->shouldReceive('save')->with($irfoGvPermit2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $irfoGvPermit1->getOrganisation());
        $this->assertSame($toOrganisation, $irfoGvPermit2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '2 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandIrfoPsvAuths()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $irfoPsvAuth1= new \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth(
            $fromOrganisation,
            new \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType(),
            new RefData()
        );
        $irfoPsvAuth2= new \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth(
            $fromOrganisation,
            new \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType(),
            new RefData()
        );

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$irfoPsvAuth1, $irfoPsvAuth2]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['IrfoPsvAuth']->shouldReceive('save')->with($irfoPsvAuth1)->once();
        $this->repoMap['IrfoPsvAuth']->shouldReceive('save')->with($irfoPsvAuth2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $irfoPsvAuth1->getOrganisation());
        $this->assertSame($toOrganisation, $irfoPsvAuth2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '2 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandTask()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $task1 = new \Dvsa\Olcs\Api\Entity\Task\Task(
            new \Dvsa\Olcs\Api\Entity\System\Category(),
            new \Dvsa\Olcs\Api\Entity\System\SubCategory()
        );
        $task1->setIrfoOrganisation($fromOrganisation);
        $task2 = new \Dvsa\Olcs\Api\Entity\Task\Task(
            new \Dvsa\Olcs\Api\Entity\System\Category(),
            new \Dvsa\Olcs\Api\Entity\System\SubCategory()
        );
        $task2->setIrfoOrganisation($fromOrganisation);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$task1, $task2]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['Task']->shouldReceive('save')->with($task1)->once();
        $this->repoMap['Task']->shouldReceive('save')->with($task2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $task1->getIrfoOrganisation());
        $this->assertSame($toOrganisation, $task2->getIrfoOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '2 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandDisqualification()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $disqualification = new \Dvsa\Olcs\Api\Entity\Organisation\Disqualification($fromOrganisation);
        $fromOrganisation->addDisqualifications($disqualification);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['Disqualification']->shouldReceive('save')->with($disqualification)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $disqualification->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '1 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandEbsrSubmission()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $ebsrSubmission1 = new \Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission();
        $ebsrSubmission1->setOrganisation($fromOrganisation);
        $ebsrSubmission2 = new \Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission();
        $ebsrSubmission2->setOrganisation($fromOrganisation);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$ebsrSubmission1, $ebsrSubmission2]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['EbsrSubmission']->shouldReceive('save')->with($ebsrSubmission1)->once();
        $this->repoMap['EbsrSubmission']->shouldReceive('save')->with($ebsrSubmission2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $ebsrSubmission1->getOrganisation());
        $this->assertSame($toOrganisation, $ebsrSubmission2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '2 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandTxcInbox()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $txcInbox1 = new \Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox();
        $txcInbox1->setOrganisation($fromOrganisation);
        $txcInbox2 = new \Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox();
        $txcInbox2->setOrganisation($fromOrganisation);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$txcInbox1, $txcInbox2]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['TxcInbox']->shouldReceive('save')->with($txcInbox1)->once();
        $this->repoMap['TxcInbox']->shouldReceive('save')->with($txcInbox2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $txcInbox1->getOrganisation());
        $this->assertSame($toOrganisation, $txcInbox2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '2 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandEventHistory()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $eventHistory1 = new \Dvsa\Olcs\Api\Entity\EventHistory\EventHistory();
        $eventHistory1->setOrganisation($fromOrganisation);
        $eventHistory2 = new \Dvsa\Olcs\Api\Entity\EventHistory\EventHistory();
        $eventHistory2->setOrganisation($fromOrganisation);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([$eventHistory1, $eventHistory2]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['EventHistory']->shouldReceive('save')->with($eventHistory1)->once();
        $this->repoMap['EventHistory']->shouldReceive('save')->with($eventHistory2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $eventHistory1->getOrganisation());
        $this->assertSame($toOrganisation, $eventHistory2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '2 EventHistory(s) transferred',
            '0 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }

    public function testHandleCommandUsers()
    {
        $data = [
            'id' => 12,
            'receivingOrganisation' => 12,
        ];
        $command = Cmd::create($data);

        $fromOrganisation = new Organisation();
        $toOrganisation = new Organisation();

        $organisationUser1 = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser();
        $organisationUser1->setOrganisation($fromOrganisation);
        $fromOrganisation->addOrganisationUsers($organisationUser1);
        $organisationUser2 = new \Dvsa\Olcs\Api\Entity\EventHistory\EventHistory();
        $organisationUser2->setOrganisation($fromOrganisation);
        $fromOrganisation->addOrganisationUsers($organisationUser2);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($fromOrganisation);
        $this->repoMap['Organisation']->shouldReceive('fetchById')->with(12)->once()->andReturn($toOrganisation);

        $this->repoMap['Note']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['Task']->shouldReceive('fetchByIrfoOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EbsrSubmission']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['TxcInbox']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);
        $this->repoMap['EventHistory']->shouldReceive('fetchByOrganisation')->with($fromOrganisation)->once()
            ->andReturn([]);

        $this->repoMap['Organisation']->shouldReceive('delete')->with($fromOrganisation)->once();

        $this->repoMap['OrganisationUser']->shouldReceive('save')->with($organisationUser1)->once();
        $this->repoMap['OrganisationUser']->shouldReceive('save')->with($organisationUser2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($toOrganisation, $organisationUser1->getOrganisation());
        $this->assertSame($toOrganisation, $organisationUser2->getOrganisation());

        $expectedResult = [
            '0 Licence(s) transferred',
            '0 Note(s) transferred',
            '0 IrfoGvPermit(s) transferred',
            '0 IrfoPsvAuth(s) transferred',
            '0 Task(s) transferred',
            '0 Disqualifications(s) transferred',
            '0 EbsrSubmission(s) transferred',
            '0 TxcInbox(s) transferred',
            '0 EventHistory(s) transferred',
            '2 OrganisationUser(s) transferred',
        ];

        $this->assertSame($expectedResult, $result->getMessages());
    }
}
