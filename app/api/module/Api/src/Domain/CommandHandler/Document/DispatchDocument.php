<?php

/**
 * Dispatch Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $this->checkCommandParams($command);

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        if ($licence->getTranslateToWelsh() === 'Y') {
            $result->merge($this->generateTranslationTask($licence, $command->getDescription()));
        }

        $documentResult = $this->proxyCommand($command, CreateDocumentSpecificCmd::class);
        $result->merge($documentResult);

        if ($licence->getOrganisation()->getAllowEmail() === 'N'
            || !$this->hasAdminEmailAddresses($licence->getOrganisation())
        ) {
            return $this->attemptPrint($documentResult->getId('document'), $command->getDescription(), $result);
        }

        $result->merge(
            $this->sendMessage(
                $licence,
                $documentResult->getId('document'),
                $command->getSubCategory()
            )
        );

        return $result;
    }

    /**
     * @param LicenceEntity $licence
     * @param int $documentId
     * @param int $subCategoryId
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

    protected function attemptPrint($documentId, $description, Result $result)
    {
        $dtoData = ['documentId' => $documentId, 'jobName' => $description];

        $result->merge($this->handleSideEffect(Enqueue::create($dtoData)));

        return $result;
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
