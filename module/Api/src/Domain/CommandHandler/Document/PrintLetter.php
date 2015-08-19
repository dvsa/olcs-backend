<?php

/**
 * Print Letter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTranslateToWelshTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter as Cmd;

/**
 * Print Letter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintLetter extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Document';

    protected $extraRepos = ['DocTemplate'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var Entity\Doc\Document $document */
        $document = $this->getRepo()->fetchUsingId($command);

        if ($this->shouldEmail($document, $command)) {
            return $this->sendEmail($document);
        }

        if ($this->isOn($this->getLicenceFromDocument($document)->getTranslateToWelsh())) {
            return $this->createTranslationTask($document, $document->getDescription());
        }

        return $this->attemptPrint($document);
    }

    /**
     * Find a related licence
     *
     * @param Entity\Doc\Document $document
     * @return Entity\Licence\Licence|null
     */
    protected function getLicenceFromDocument(Entity\Doc\Document $document)
    {
        // If we have linked the doc directly to the licence
        $licence = $document->getLicence();
        if ($licence !== null) {
            return $licence;
        }

        // If we have linked the doc to an application
        $application = $document->getApplication();
        if ($application !== null) {
            return $application->getLicence();
        }

        // If we have linked the doc to a case
        $case = $document->getCase();
        if ($case !== null) {

            // If the case is a licence case
            $licence = $case->getLicence();
            if ($licence !== null) {
                return $licence;
            }
        }

        // If we have linked the doc to a bus reg
        $busReg = $document->getBusReg();
        if ($busReg !== null) {

            // If the bus reg is linked to a licence
            $licence = $busReg->getLicence();
            if ($licence !== null) {
                return $licence;
            }
        }

        // @NOTE Add other methods of determining the licence from the document record as necessary

        return null;
    }

    /**
     * Check if we SHOULD email the document to the operator
     *
     * @param Entity\Doc\Document $document
     * @param Cmd $command
     * @return bool
     * @throws RequiresConfirmationException
     */
    protected function shouldEmail(Entity\Doc\Document $document, Cmd $command)
    {
        if ($this->canEmail($document)) {

            if ($command->getShouldEmail() === null) {
                throw new RequiresConfirmationException('Should email', 'should_email');
            }

            return $command->getShouldEmail() === 'Y';
        }

        return false;
    }

    /**
     * Check if we CAN email the document to the operator
     *
     * @param Entity\Doc\Document $document
     * @return bool
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function canEmail(Entity\Doc\Document $document)
    {
        $licence = $this->getLicenceFromDocument($document);

        // If we can't find a licence
        // OR if the allow email preference is off
        if ($licence === null || !$this->isOn($licence->getOrganisation()->getAllowEmail())) {
            return false;
        }

        $metadata = json_decode($document->getMetadata(), true);

        $templateId = $metadata['details']['documentTemplate'];

        /** @var Entity\Doc\DocTemplate $template */
        $template = $this->getRepo('DocTemplate')->fetchById($templateId);

        // If the document is suppressed
        if ($this->isOn($template->getSuppressFromOp())) {
            return false;
        }

        return true;
    }

    /**
     * @NOTE This might be useful elsewhere
     *
     * @param $value
     * @return bool
     */
    protected function isOn($value)
    {
        return ($value === 'Y' || $value === true || $value == 1);
    }

    /**
     * Attempt to print
     *
     * @param Entity\Doc\Document $document
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function attemptPrint(Entity\Doc\Document $document)
    {
        $dtoData = ['fileIdentifier' => $document->getIdentifier(), 'jobName' => $document->getDescription()];

        return $this->handleSideEffect(Enqueue::create($dtoData));
    }

    /**
     * Create translation task
     *
     * @param Entity\Licence\Licence $licence
     * @param $description
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function createTranslationTask(Entity\Licence\Licence $licence, $description)
    {
        $data = [
            'description' => $description,
            'licence' => $licence->getId()
        ];

        return $this->handleSideEffect(CreateTranslateToWelshTask::create($data));
    }

    /**
     * Send email
     *
     * @param Entity\Doc\Document $document
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function sendEmail(Entity\Doc\Document $document)
    {
        $licence = $this->getLicenceFromDocument($document);

        $dtoData = [
            'licence' => $licence->getId(),
            'document' => $document->getId(),
            'type' => CreateCorrespondenceRecord::TYPE_STANDARD,
        ];

        return $this->handleSideEffect(CreateCorrespondenceRecord::create($dtoData));
    }
}
