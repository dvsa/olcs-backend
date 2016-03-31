<?php

/**
 * Update Document Links
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\Document\UpdateDocumentLinks as Cmd;
use Dvsa\Olcs\Api\Entity;

/**
 * Update Document Links
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateDocumentLinks extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        $document = $this->getRepo()->fetchUsingId($command);

        $this->linkDocument($document, $command);

        $this->getRepo()->save($document);

        return $this->result;
    }

    private function linkDocument(Entity\Doc\Document $document, Cmd $command)
    {
        $this->maybeLinkDocument($document, $command, Entity\Application\Application::class, 'Application');
        $this->maybeLinkDocument($document, $command, Entity\Bus\BusReg::class, 'BusReg');
        $this->maybeLinkDocument($document, $command, Entity\Cases\Cases::class, 'Case');
        $this->maybeLinkDocument($document, $command, Entity\Cases\Statement::class, 'Statement');
        $this->maybeLinkDocument($document, $command, Entity\Licence\Licence::class, 'Licence');
        $this->maybeLinkDocument($document, $command, Entity\Organisation\Organisation::class, 'IrfoOrganisation');
        $this->maybeLinkDocument($document, $command, Entity\Submission\Submission::class, 'Submission');
        $this->maybeLinkDocument($document, $command, Entity\TrafficArea\TrafficArea::class, 'TrafficArea');
        $this->maybeLinkDocument($document, $command, Entity\Tm\TransportManager::class, 'TransportManager');
        $this->maybeLinkDocument($document, $command, Entity\OperatingCentre\OperatingCentre::class, 'OperatingCentre');
    }

    private function maybeLinkDocument(Entity\Doc\Document $document, Cmd $command, $entity, $suffix)
    {
        $getter = 'get' . $suffix;
        $setter = 'set' . $suffix;
        $value = $command->{$getter}();

        if ($value !== null) {

            $this->result->addMessage('Document linked to ' . $suffix . ': ' . $value);

            $reference = $this->getRepo()->getReference($entity, $value);
            $document->{$setter}($reference);
        }
    }
}
