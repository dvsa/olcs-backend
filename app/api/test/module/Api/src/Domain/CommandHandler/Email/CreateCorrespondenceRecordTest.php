<?php

/**
 * Create Correspondence Record Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\CreateCorrespondenceRecord as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\CorrespondenceInbox as CorrespondenceInboxRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;

/**
 * Create Correspondence Record Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateCorrespondenceRecordTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('CorrespondenceInbox', CorrespondenceInboxRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Licence::class => [
                111 => m::mock(Licence::class)
            ],
            Document::class => [
                222 => m::mock(Document::class)
            ]
        ];

        parent::initReferences();
    }


    public function testHandleCommand()
    {
        $data = [
            'licence' => 111,
            'document' => 222,
            'type' => 'standard'
        ];
        $command = Cmd::create($data);

        /** @var User $user1 */
        $user1 = m::mock(User::class)->makePartial();
        $contactDetails1 = m::mock(ContactDetails::class)->makePartial();
        $contactDetails1->setEmailAddress('foo1@bar.com');
        $user1->setContactDetails($contactDetails1);

        /** @var User $user2 */
        $user2 = m::mock(User::class)->makePartial();
        $contactDetails2 = m::mock(ContactDetails::class)->makePartial();
        $contactDetails2->setEmailAddress('foo2@bar.com');
        $user2->setContactDetails($contactDetails2);

        /** @var OrganisationUser $orgUser1 */
        $orgUser1 = m::mock(OrganisationUser::class)->makePartial();
        $orgUser1->setUser($user1);
        /** @var OrganisationUser $orgUser2 */
        $orgUser2 = m::mock(OrganisationUser::class)->makePartial();
        $orgUser2->setUser($user2);

        /** @var Organisation $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->shouldReceive('getAdminOrganisationUsers')
            ->andReturn([$orgUser1, $orgUser2]);

        $this->references[Licence::class][111]->setOrganisation($organisation);
        $this->references[Licence::class][111]->setTranslateToWelsh(true);
        $this->references[Licence::class][111]->setLicNo('AB12345678');

        $this->repoMap['CorrespondenceInbox']->shouldReceive('save')
            ->with(m::type(CorrespondenceInbox::class))
            ->andReturnUsing(
                function (CorrespondenceInbox $record) {
                    $record->setId(123);
                    $this->assertSame($this->references[Licence::class][111], $record->getLicence());
                    $this->assertSame($this->references[Document::class][222], $record->getDocument());
                }
            );

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')
            ->times(2)
            ->with(
                m::type(Message::class),
                'licensing-information-standard',
                ['licNo' => 'AB12345678', 'url' => 'http://selfserve/correspondence'],
                null
            );

        $result = new Result();

        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'foo1@bar.com'
            ],
            $result
        );

        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'foo2@bar.com'
            ],
            $result
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'correspondenceInbox' => 123
            ],
            'messages' => [
                'Correspondence record created',
                'Email sent'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
