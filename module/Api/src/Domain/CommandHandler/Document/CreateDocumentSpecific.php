<?php

/**
 * Create Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as Cmd;

/**
 * Create Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateDocumentSpecific extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $document = $this->createDocumentEntity($command);

        $this->getRepo()->save($document);

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
        $this->linkDocument($document, $command);
        $this->setDocumentDetails($document, $command);
        $this->setDocumentFlags($document, $command);

        return $document;
    }

    private function setDocumentFlags(Document $document, Cmd $command)
    {
        $document->setIsExternal($command->getIsExternal());
        $document->setIsReadOnly($command->getIsReadOnly());
        $document->setIsScan($command->getIsScan());
    }

    private function setDocumentDetails(Document $document, Cmd $command)
    {
        $document->setFilename($command->getFilename());
        $document->setSize($command->getSize());
        $document->setDescription($command->getDescription());

        if ($command->getIssuedDate() !== null) {
            $document->setIssuedDate(new \DateTime($command->getIssuedDate()));
        }
    }

    private function linkDocument(Document $document, Cmd $command)
    {
        $this->maybeLinkDocument($document, $command, Application::class, 'Application');
        $this->maybeLinkDocument($document, $command, BusReg::class, 'BusReg');
        $this->maybeLinkDocument($document, $command, Cases::class, 'Case');
        $this->maybeLinkDocument($document, $command, Organisation::class, 'IrfoOrganisation');
        $this->maybeLinkDocument($document, $command, Submission::class, 'Submission');
        $this->maybeLinkDocument($document, $command, TrafficArea::class, 'TrafficArea');
        $this->maybeLinkDocument($document, $command, TransportManager::class, 'TransportManager');
        $this->maybeLinkDocument($document, $command, Licence::class, 'Licence');
        $this->maybeLinkDocument($document, $command, OperatingCentre::class, 'OperatingCentre');
        $this->maybeLinkDocument($document, $command, Opposition::class, 'Opposition');
    }

    private function maybeLinkDocument(Document $document, Cmd $command, $entity, $suffix)
    {
        $getter = 'get' . $suffix;
        $setter = 'set' . $suffix;
        $value = $command->{$getter}();

        if ($value !== null) {
            $reference = $this->getRepo()->getReference($entity, $value);
            $document->{$setter}($reference);
        }
    }

    private function categoriseDocument(Document $document, Cmd $command)
    {
        if ($command->getCategory() != null) {
            $document->setCategory($this->getRepo()->getCategoryReference($command->getCategory()));
        }

        if ($command->getSubCategory() != null) {
            $document->setSubCategory($this->getRepo()->getCategoryReference($command->getSubCategory()));
        }
    }
}
