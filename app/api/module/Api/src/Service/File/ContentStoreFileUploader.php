<?php

namespace Dvsa\Olcs\Api\Service\File;

use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Dvsa\Olcs\DocumentShare\Service\Client as ContentStoreClient;
use Zend\Http\Response;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Content Store File Uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContentStoreFileUploader implements FileUploaderInterface, FactoryInterface
{
    const ERR_UNABLE_UPLOAD = 'Unable to store uploaded file: %s';

    /** @var File */
    private $file;
    /** @var ContentStoreClient */
    private $contentStoreClient;

    /**
     * Method-factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->contentStoreClient = $serviceLocator->get('ContentStore');

        return $this;
    }

    /**
     * Getter for file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Setter for file
     *
     * @param File $file File
     *
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Upload file to remote storage
     *
     * @param string $identifier File name on Storage
     *
     * @return \Dvsa\Olcs\Api\Service\File\File
     * @throws Exception
     * @throws MimeNotAllowedException
     */
    public function upload($identifier)
    {
        $file = $this->file;

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent());

        $response = $this->write($identifier, $storeFile);

        if ($response->isSuccess()) {
            $file->setPath($identifier);
            $file->setIdentifier($identifier);

            return $file;
        }

        if ($response->getStatusCode() === Response::STATUS_CODE_415) {
            throw new MimeNotAllowedException();
        }

        throw new Exception(sprintf(self::ERR_UNABLE_UPLOAD, $response->getBody()));
    }

    /**
     * Download file from remote storage
     *
     * @param string $identifier File name on storage
     *
     * @return ContentStoreFile|null
     */
    public function download($identifier)
    {
        return $this->contentStoreClient->read($identifier);
    }

    /**
     * Remove the file from remote storage
     *
     * @param string $identifier File name on storage
     *
     * @return Response
     */
    public function remove($identifier)
    {
        return $this->contentStoreClient->remove($identifier);
    }

    /**
     * Write file to remote storage
     *
     * @param string           $identifier File name of storage
     * @param ContentStoreFile $file       File
     *
     * @return Response
     */
    private function write($identifier, ContentStoreFile $file)
    {
        return $this->contentStoreClient->write($identifier, $file);
    }
}
