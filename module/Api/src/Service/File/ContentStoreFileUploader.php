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
    const ERR_UNABLE_UPLOAD = 'Unable to store uploaded file';

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
     * Upload file to remote storage
     *
     * @param string           $identifier File name on Storage
     * @param ContentStoreFile $file       Uploded File
     *
     * @return ContentStoreFile
     * @throws Exception
     * @throws MimeNotAllowedException
     */
    public function upload($identifier, ContentStoreFile $file)
    {
        $file->setIdentifier($identifier);

        $response = $this->write($identifier, $file);

        if ($response !== false) {
            return $file;
        }

        throw new Exception(self::ERR_UNABLE_UPLOAD);
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
        $file = $this->contentStoreClient->read($identifier);

        if ($file !== false) {
            return $file;
        }

        return null;
    }

    /**
     * Remove the file from remote storage
     *
     * @param string $identifier File name on storage
     *
     * @return bool
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
     * @return bool
     */
    private function write($identifier, ContentStoreFile $file)
    {
        return $this->contentStoreClient->write($identifier, $file);
    }
}
