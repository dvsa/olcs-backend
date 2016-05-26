<?php

/**
 * Create Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateSubmission as CreateEbsrSubmissionCmd;
use Dvsa\Olcs\Transfer\Command\Document\UpdateDocumentLinks;

/**
 * Create Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateDocumentSpecific extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $document = $this->createDocumentEntity($command);

        $this->getRepo()->save($document);

        $data = $command->getArrayCopy();
        $data['id'] = $document->getId();

        $result->merge($this->handleSideEffect(UpdateDocumentLinks::create($data)));

        //if the document is an EBSR pack, create a corresponding EBSR submission
        if ($data['isEbsrPack']) {
            $result->merge($this->handleSideEffect(CreateEbsrSubmissionCmd::create(['document' => $data['id']])));
        }

        $result->addId('document', $document->getId());
        $result->addMessage('Document created');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Document
     */
    private function createDocumentEntity(Cmd $command)
    {
        $document = new Document($command->getIdentifier());

        $this->categoriseDocument($document, $command);
        $this->setDocumentDetails($document, $command);
        $this->setDocumentFlags($document, $command);

        return $document;
    }

    /**
     * @param Document $document
     * @param Cmd $command
     */
    private function setDocumentFlags(Document $document, Cmd $command)
    {
        $document->setIsExternal($command->getIsExternal());
        $document->setIsScan($command->getIsScan());
    }

    /**
     * @param Document $document
     * @param Cmd $command
     */
    private function setDocumentDetails(Document $document, Cmd $command)
    {
        $document->setFilename($command->getFilename());
        $document->setSize($command->getSize());
        $document->setDescription($command->getDescription());

        if ($command->getIssuedDate() !== null) {
            $document->setIssuedDate(new \DateTime($command->getIssuedDate()));
        }

        if ($command->getMetadata() !== null) {
            $document->setMetadata($command->getMetadata());
        }
    }

    /**
     * @param Document $document
     * @param Cmd $command
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function categoriseDocument(Document $document, Cmd $command)
    {
        if ($command->getCategory() != null) {
            $document->setCategory($this->getRepo()->getCategoryReference($command->getCategory()));
        }

        if ($command->getSubCategory() != null) {
            $document->setSubCategory($this->getRepo()->getSubCategoryReference($command->getSubCategory()));
        }
    }
}
