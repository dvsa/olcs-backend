<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Command\Email\SendPtrNotificationForRegisteredUser;
use Dvsa\Olcs\Api\Domain\Command\Email\SendPtrNotificationForUnregisteredUser;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\DeleteDocument;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetters;

class ProposeToRevoke extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait, QueueAwareTrait;

    protected $repoServiceName = 'Document';

    protected $extraRepos = [
        'Licence'
    ];

    /**
     * @var Licence
     */
    protected $licenceEntity;

    protected $licenceId;

    /**
     * @inheritDoc
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->licenceId = (int)$command->getLicence();
        $this->licenceEntity = $this->getRepo('Licence')->fetchById($this->licenceId);

        $templateDocument = $command->getDocument();

        $this->generateLetters($templateDocument);
        $this->printLetters();
        $this->createTask();
        $this->sendEmails();
        $this->result->addMessage('Propose to revoke successfully processed');

        return $this->result;
    }

    protected function createTask(): void
    {
        $currentUser = $this->getCurrentUser();

        $taskData = [
            'category' => Category::CATEGORY_COMPLIANCE,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_IN_OFFICE_REVOCATION,
            'description' => 'Check response to PTR',
            'actionDate' => (new DateTime('now'))->add(new \DateInterval('P21D'))->format('Y-m-d'),
            'assignedToUser' => $currentUser->getId(),
            'assignedToTeam' => $currentUser->getTeam()->getId(),
            'licence' => $this->licenceId,
            'urgent' => 'Y'
        ];

        $this->result->merge($this->handleSideEffect(CreateTask::create($taskData)));
    }

    protected function generateLetters(int $documentID): void
    {
        $document = $this->getRepo('Document')->fetchById($documentID);

        $data = [
            'generateCommandData' => [
                'template' => $documentID,
                'licence' => $this->licenceId,
                'query' => [
                    'licence' => $this->licenceId
                ],
                'category' => Category::CATEGORY_COMPLIANCE,
                'subCategory' => SubCategory::DOC_SUB_CATEGORY_IN_OFFICE_REVOCATION,
                'isExternal' => false,
                'description' => $document->getDescription()
            ],
            'addressBookmark' => 'ptr_correspondent_address',
            'bookmarkBundle' => [
                'correspondenceCd' => ['address']
            ],
        ];

        $this->result->merge($this->handleSideEffect(GenerateAndStoreWithMultipleAddresses::create($data)));
        $this->result->merge($this->handleSideEffect(DeleteDocument::create(['id' => $documentID])));
    }

    private function printLetters(): void
    {
        $this->result->merge($this->handleSideEffect(PrintLetters::create(
            [
                'ids' => $this->result->getIds()['documents'],
                'method' => PrintLetters::METHOD_PRINT_AND_POST
            ]
        )));
    }

    private function sendEmails(): void
    {
        $translateToWelsh = $this->licenceEntity->getTranslateToWelsh();
        $correspondenceEmail = $this->licenceEntity->getCorrespondenceCd()->getEmailAddress();

        $selfServeUserEmailCommands = array_map(
            function ($organisationUser) use ($translateToWelsh) {
                $selfServeUserEmailCommandsData = [
                    'emailAddress' => $organisationUser->getUser()->getContactDetails()->getEmailAddress(),
                    'translateToWelsh' => $translateToWelsh
                ];
                return $this->emailQueue(
                    SendPtrNotificationForRegisteredUser::class,
                    $selfServeUserEmailCommandsData,
                    $this->licenceId
                );
            },
            $this->licenceEntity->getOrganisation()->getAdministratorUsers()->toArray()
        );

        if (is_null($correspondenceEmail) && empty($selfServeUserEmailCommands)) {
            $this->result->addMessage('Unable to send emails: No email addresses found');
            return;
        }

        if (!is_null($correspondenceEmail)) {
            $this->sendCorrespondenceEmail(
                $correspondenceEmail,
                $translateToWelsh,
                !empty($selfServeUserEmailCommands)
            );
        }

        foreach ($selfServeUserEmailCommands as $selfServeUserEmailCommand) {
            $this->result->merge($this->handleSideEffect($selfServeUserEmailCommand));
        }
    }

    private function sendCorrespondenceEmail(string $email, string $translateToWelsh, bool $isRegistered): void
    {
        $cmdData = [
            'emailAddress' => $email,
            'translateToWelsh' => $translateToWelsh
        ];

        if (!$isRegistered) {
            $cmdData['docs'] = [$this->result->getIds()['correspondenceAddress']];
            $cmd = $this->emailQueue(SendPtrNotificationForUnregisteredUser::class, $cmdData, $this->licenceId);
        } else {
            $cmd = $this->emailQueue(SendPtrNotificationForRegisteredUser::class, $cmdData, $this->licenceId);
        }

        $this->result->merge($this->handleSideEffect($cmd));
    }
}
