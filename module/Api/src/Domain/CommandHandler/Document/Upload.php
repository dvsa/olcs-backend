<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareTrait;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

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
    const ERR_EBSR_MIME = 'ERR_EBSR_MIME';

    use UploaderAwareTrait,
        NamingServiceAwareTrait;

    protected $repoServiceName = 'Document';

    /**
     * Execute command
     *
     * @param TransferCmd\Document\Upload $command Command
     *
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        $identifier = $this->determineIdentifier($command);

        $file = $this->uploadFile($command, $identifier);

        $this->result->merge($this->createDocument($command, $file, $identifier));

        return $this->result;
    }

    /**
     * Define file name(path)
     *
     * @param TransferCmd\Document\Upload $command Upload command
     *
     * @return string
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function determineIdentifier(TransferCmd\Document\Upload $command)
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

    /**
     * Upload file to Document storage
     *
     * @param TransferCmd\Document\Upload $command    Upload Command
     * @param string                      $identifier File name (path)
     *
     * @return File
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Service\File\Exception
     * @throws \Exception
     */
    protected function uploadFile(TransferCmd\Document\Upload $command, $identifier)
    {
        $file = new File();
        $file->setName($command->getFilename());

        $content = $command->getContent();
        $file->setContent(!is_array($content) ? base64_decode($content) : $content);

        if ($command->getIsEbsrPack() && $file->getMimeType() !== 'application/zip') {
            throw new ValidationException([self::ERR_EBSR_MIME => self::ERR_EBSR_MIME]);
        }

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

    /**
     * Create document
     *
     * @param TransferCmd\Document\Upload $command    Upload command
     * @param File                        $file       File
     * @param string                      $identifier File name (path)
     *
     * @return Result
     */
    protected function createDocument(TransferCmd\Document\Upload $command, File $file, $identifier)
    {
        $data = $command->getArrayCopy();
        unset($data['content']);

        $data['identifier'] = $file->getIdentifier();
        $data['size'] = $file->getSize();
        $data['filename'] = $identifier;
        $data['description'] = $this->getDescriptionFromCommand($command);
        $data['user'] = $command->getUser();

        if ($data['isExternal'] === null) {
            $cmd = DomainCmd\Document\CreateDocument::create($data);
        } else {
            $cmd = DomainCmd\Document\CreateDocumentSpecific::create($data);
        }

        return $this->handleSideEffect($cmd);
    }

    /**
     * Get description from command data
     *
     * @param TransferCmd\Document\Upload $command Upload command
     *
     * @return string
     */
    protected function getDescriptionFromCommand(TransferCmd\Document\Upload $command)
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
