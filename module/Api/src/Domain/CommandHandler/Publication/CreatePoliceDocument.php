<?php

/**
 * Creates the police version of a publication
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Publication\CreateNextPublication as CreateNextPublicationCmd;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * Creates the police version of a publication
 */
final class CreatePoliceDocument extends AbstractCommandHandler implements 
    TransactionedInterface,
    UploaderAwareInterface
{
    use UploaderAwareTrait;
    
    protected $repoServiceName = 'Publication';

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PublicationEntity $publication
         * @var CreateNextPublicationCmd
         */
        $publication = $this->getRepo()->fetchUsingId($command);

        /**
         * @todo the actual logic for modifying the police version of the document will be added as part
         * of OLCS-10704. For now we just re-upload the same document
         *
         * @var File $previousDocument
         */
        $previousDocument = $this->getUploader()->download($publication->getDocument()->getIdentifier());

        return $this->handleSideEffect($this->persistPoliceDoc($previousDocument, $publication));
    }

    /**
     * @param File $previousDocument
     * @param PublicationEntity $publication
     * @return UploadCmd
     */
    private function persistPoliceDoc(File $previousDocument, PublicationEntity $publication)
    {
        //document upload expects a filename so it can determine the file extension, but this will be overwritten later
        $filename = basename($publication->getDocument()->getFilename());

        $data = [
            'content' => base64_encode($previousDocument->getContent()),
            'description' => $publication->getDocTemplate()->getDescription() . ' police version',
            'category' => $publication->getDocTemplate()->getCategory()->getId(),
            'subCategory' => $publication->getDocTemplate()->getSubCategory()->getId(),
            'isExternal' => true,
            'isReadOnly' => 'Y',
            'filename' => $filename
        ];

        return UploadCmd::create($data);
    }
}
