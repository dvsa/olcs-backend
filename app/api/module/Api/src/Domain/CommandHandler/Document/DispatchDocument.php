<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecificCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTranslateToWelshTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Dispatch Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DispatchDocument extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    /**
     * Handler
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->checkCommandParams($command);

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        if ($licence->getTranslateToWelsh() === 'Y') {
            $this->result->merge(
                $this->generateTranslationTask($licence, $command->getDescription())
            );
        }

        $documentResult = $this->proxyCommand($command, CreateDocumentSpecificCmd::class);
        $this->result->merge($documentResult);

        if (
            $licence->getOrganisation()->getAllowEmail() === 'N'
            || !$this->hasAdminEmailAddresses($licence->getOrganisation())
            || $command->getPrintCopiesCount() > 1
        ) {
            $this->result->merge(
                $this->attemptPrint(
                    $documentResult->getId('document'),
                    $command->getDescription(),
                    ($command->getUser() ? $command->getUser() : $this->getCurrentUser()),
                    $command->getPrintCopiesCount()
                )
            );

            return $this->result;
        }

        $this->result->merge(
            $this->sendMessage(
                $licence,
                $documentResult->getId('document'),
                $command->getSubCategory()
            )
        );

        return $this->result;
    }

    /**
     * Send Message
     *
     * @param LicenceEntity $licence       Licence
     * @param int           $documentId    Document Id
     * @param int           $subCategoryId Sub Cat Idf
     *
     * @return Result
     */
    protected function sendMessage(LicenceEntity $licence, $documentId, $subCategoryId)
    {
        if ($subCategoryId === Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE) {
            $type = CreateCorrespondenceRecord::TYPE_CONTINUATION;
        } else {
            $type = CreateCorrespondenceRecord::TYPE_STANDARD;
        }

        $dtoData = [
            'licence' => $licence->getId(),
            'document' => $documentId,
            'type' => $type,
        ];

        return $this->handleSideEffect(CreateCorrespondenceRecord::create($dtoData));
    }

    protected function checkCommandParams(Cmd $command)
    {
        if ($command->getLicence() === null) {
            throw new BadRequestException('Please provide a licence parameter');
        }

        if ($command->getDescription() === null) {
            throw new BadRequestException('Please provide a document description parameter');
        }
    }

    protected function hasAdminEmailAddresses(Organisation $organisation)
    {
        /** @var OrganisationUser $orgUser */
        foreach ($organisation->getAdminOrganisationUsers() as $orgUser) {
            if ($orgUser->getUser()->getContactDetails()->getEmailAddress() !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create print queue
     *
     * @param int    $documentId  Document id
     * @param string $description Job name
     * @param int    $user        User
     * @param int    $copiesCount Count of copies to print
     *
     * @return Result
     */
    protected function attemptPrint($documentId, $description, $user, $copiesCount)
    {
        return $this->handleSideEffect(
            Enqueue::create(
                [
                    'documentId' => $documentId,
                    'jobName' => $description,
                    'user' => $user,
                    'copies' => $copiesCount,
                ]
            )
        );
    }

    protected function generateTranslationTask(LicenceEntity $licence, $description)
    {
        $data = [
            'description' => $description,
            'licence' => $licence->getId()
        ];

        return $this->handleSideEffect(CreateTranslateToWelshTask::create($data));
    }
}
