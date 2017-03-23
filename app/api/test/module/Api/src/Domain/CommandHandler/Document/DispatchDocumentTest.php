<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTranslateToWelshTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\DispatchDocument as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Dispatch Document Test
 *
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Document\DispatchDocument
 */
class DispatchDocumentTest extends CommandHandlerTestCase
{
    /** @var \Dvsa\Olcs\Api\Domain\CommandHandler\Document\DispatchDocument */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleCommandNoLicence()
    {
        $this->setExpectedException(BadRequestException::class);

        $data = [];
        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandNoDescription()
    {
        $this->setExpectedException(BadRequestException::class);

        $data = [
            'licence' => 111
        ];
        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandEmail()
    {
        $data = [
            'licence' => 111,
            'description' => 'foo',
            'user' => 1,
        ];
        $command = Cmd::create($data);

        /** @var User $user */
        $user = m::mock(User::class)->makePartial();
        $contactDetails = m::mock(ContactDetails::class)->makePartial();
        $contactDetails->setEmailAddress('foo@bar.com');
        $user->setContactDetails($contactDetails);

        /** @var OrganisationUser $orgUser */
        $orgUser = m::mock(OrganisationUser::class)->makePartial();
        $orgUser->setUser($user);

        /** @var Organisation $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->setAllowEmail('Y');
        $organisation->shouldReceive('getAdminOrganisationUsers')
            ->andReturn([$orgUser]);

        /** @var Licence  $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(111);
        $licence->setTranslateToWelsh('N');
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $result1 = new Result();
        $result1->addId('document', 123);
        $dataForCreateDocSpecific = [
            'licence' => 111,
            'description' => 'foo',
            'user' => 1,
        ];
        $commandCreateDocSpecific = Cmd::create($dataForCreateDocSpecific);
        $this->expectedSideEffect(CreateDocumentSpecific::class, $commandCreateDocSpecific->getArrayCopy(), $result1);

        $data = [
            'licence' => 111,
            'document' => 123,
            'type' => 'standard'
        ];
        $result2 = new Result();
        $result2->addId('correspondenceInbox', 321);
        $this->expectedSideEffect(CreateCorrespondenceRecord::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 123,
                'correspondenceInbox' => 321
            ],
            'messages' => [

            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPrint()
    {
        $data = [
            'licence' => 111,
            'identifier' => 'ABC123',
            'description' => 'foo',
            'user' => 1,
            'printCopiesCount' => 888,
        ];
        $command = Cmd::create($data);

        /** @var Organisation $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->setAllowEmail('N');

        /** @var Licence  $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(111);
        $licence->setTranslateToWelsh('N');
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $result1 = new Result();
        $result1->addId('document', 123);
        $dataForCreateDocSpecific = [
            'licence' => 111,
            'identifier' => 'ABC123',
            'description' => 'foo',
            'user' => 1
        ];
        $commandCreateDocSpecific = Cmd::create($dataForCreateDocSpecific);
        $this->expectedSideEffect(CreateDocumentSpecific::class, $commandCreateDocSpecific->getArrayCopy(), $result1);

        $data = [
            'documentId' => 123,
            'jobName' => 'foo',
            'user' => 1,
            'copies' => 888 ,
        ];
        $result2 = new Result();
        $result2->addMessage('Printed');
        $this->expectedSideEffect(Enqueue::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 123
            ],
            'messages' => [
                'Printed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPrintWelsh()
    {
        $data = [
            'licence' => 111,
            'identifier' => 'ABC123',
            'description' => 'foo',
            'user' => 1
        ];
        $command = Cmd::create($data);

        /** @var User $user */
        $user = m::mock(User::class)->makePartial();
        $contactDetails = m::mock(ContactDetails::class)->makePartial();
        $user->setContactDetails($contactDetails);

        /** @var OrganisationUser $orgUser */
        $orgUser = m::mock(OrganisationUser::class)->makePartial();
        $orgUser->setUser($user);

        /** @var Organisation $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->setAllowEmail('Y');
        $organisation->shouldReceive('getAdminOrganisationUsers')
            ->andReturn([$orgUser]);

        /** @var Licence  $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(111);
        $licence->setTranslateToWelsh('Y');
        $licence->setOrganisation($organisation);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $result1 = new Result();
        $result1->addId('document', 123);
        $dataForCreateDocSpecific = [
            'licence' => 111,
            'identifier' => 'ABC123',
            'description' => 'foo',
            'user' => 1
        ];
        $commandCreateDocSpecific = Cmd::create($dataForCreateDocSpecific);
        $this->expectedSideEffect(CreateDocumentSpecific::class, $commandCreateDocSpecific->getArrayCopy(), $result1);

        $data = [
            'documentId' => 123,
            'jobName' => 'foo',
            'user' => 1
        ];
        $result2 = new Result();
        $result2->addMessage('Printed');
        $this->expectedSideEffect(Enqueue::class, $data, $result2);

        $data = [
            'description' => 'foo',
            'licence' => 111
        ];
        $result3 = new Result();
        $result3->addMessage('Task created');
        $this->expectedSideEffect(CreateTranslateToWelshTask::class, $data, $result3);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => 123
            ],
            'messages' => [
                'Task created',
                'Printed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
