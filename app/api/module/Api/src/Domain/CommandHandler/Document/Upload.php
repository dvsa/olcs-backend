<?php

/**
 * Upload
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as CreateDocumentCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecificCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareTrait;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\Document\Upload as Cmd;

/**
 * Upload
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Upload extends AbstractCommandHandler implements
    TransactionedInterface,
    UploaderAwareInterface,
    NamingServiceAwareInterface
{
    const ERR_MIME = 'ERR_MIME';

    use UploaderAwareTrait,
        NamingServiceAwareTrait;

    protected $repoServiceName = 'Document';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $identifier = $this->determineIdentifier($command);

        $file = $this->uploadFile($command, $identifier);

        $this->result->merge($this->createDocument($command, $file, $identifier));

        return $this->result;
    }

    protected function determineIdentifier(Cmd $command)
    {
        $description = $this->getDescriptionFromCommand($command);

        $filename = $command->getFilename();
        $parts = explode('.', $filename);
        $extension = array_pop($parts);

        $category = null;
        $subCategory = null;
        $entity = $this->determineEntityFromCommand($command->getArrayCopy());

        if ($command->getCategory() !== null) {
            $category = $this->getRepo()->getCategoryReference($command->getCategory());
        }

        if ($command->getSubCategory() !== null) {
            $subCategory = $this->getRepo()->getSubCategoryReference($command->getSubCategory());
        }

        return $this->getNamingService()->generateName($description, $extension, $category, $subCategory, $entity);
    }

    protected function uploadFile(Cmd $command, $identifier)
    {
        $file = new File();
        $file->setName($command->getFilename());
        $file->setContentAndSize($command->getContent());

        try {
            $this->getUploader()->setFile($file);
            $file = $this->getUploader()->upload($identifier);
        } catch (MimeNotAllowedException $ex) {
            throw new ValidationException([self::ERR_MIME => self::ERR_MIME]);
        }

        $this->result->addMessage('File uploaded');
        $this->result->addId('identifier', $file->getIdentifier());

        return $file;
    }

    protected function createDocument(Cmd $command, File $file, $identifier)
    {
        $data = $command->getArrayCopy();
        unset($data['content']);

        $data['identifier'] = $file->getIdentifier();
        $data['size'] = $file->getSize();
        $data['filename'] = $identifier;
        $data['description'] = $this->getDescriptionFromCommand($command);
        $data['user'] = $command->getUser();

        if ($data['isExternal'] === null) {
            return $this->handleSideEffect(CreateDocumentCmd::create($data));
        } else {
            return $this->handleSideEffect(CreateDocumentSpecificCmd::create($data));
        }
    }

    protected function getDescriptionFromCommand($command)
    {
        $description = $command->getDescription();

        if ($description !== null) {
            return $description;
        }

        $filename = $command->getFilename();

        $parts = explode('.', $filename);

        array_pop($parts);

        return implode($parts);
    }
}
