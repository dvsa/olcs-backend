<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Surrender\Delete as DeleteCmd;

final class Delete extends AbstractSurrenderCommandHandler
{
    protected $repoServiceName = "Surrender";

    protected $extraRepos = ['Document'];

    /**
     * @param DeleteCmd $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $id = $command->getId();

        try {

            /** @var Surrender $surrender */
            $surrender = $this->getRepo()->fetchOneByLicenceId($id);

            $this->deleteDocuments($surrender->getId());

            $this->getRepo()->delete($surrender);

            $this->result->addId('id' . $id, $id);
            $this->result->addMessage(sprintf('surrender for licence Id %d deleted', $id));
        } catch (NotFoundException $e) {
            $this->result->addMessage(sprintf('surrender for licence Id %d not found', $id));
        }

        return $this->result;
    }

    protected function deleteDocuments($id)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Document $documentRepo */
        $documentRepo = $this->getRepo("Document");

        /** @var Document[] $documents  */
        $documents = $documentRepo->fetchListForSurrender($id);

        foreach ($documents as $document) {
            $documentRepo->hardDelete($document);
            $this->result->addId("documents", $document->getId(), true);
        }
    }
}
