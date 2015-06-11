<?php

/**
 * Create Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumetnEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as Cmd;

/**
 * Create Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateDocument extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        // Stub

        $document = $this->createDocumentObject($command);
        $this->getRepo()->save($document);

        $result = new Result();
        $result->addId('document', $document->getId());
        $result->addMessage('Document created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return DocumetnEntity
     */
    private function createDocumentObject(Cmd $command)
    {
        $document = new DocumetnEntity();

        if ($command->getLicence() !== null) {
            $licence = $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence());
        } else {
            $licence = null;
        }
        if ($command->getCategory() !== null) {
            $category = $this->getRepo()->getReference(CategoryEntity::class, $command->getCategory());
        } else {
            $category = null;
        }
        if ($command->getSubCategory() !== null) {
            $subCategory = $this->getRepo()->getReference(SubCategoryEntity::class, $command->getSubCategory());
        } else {
            $subCategory = null;
        }
        $issuedDate = new \DateTime($command->getIssuedDate());

        $document->updateDocument(
            $command->getIdentifier(),
            $command->getDescription(),
            $command->getFilename(),
            $licence,
            $category,
            $subCategory,
            $command->getIsExternal(),
            $command->getIsReadOnly(),
            $issuedDate,
            $command->getSize()
        );
        return $document;
    }
}
