<?php

/**
 * Content store file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\File;

use Zend\Http\Response;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;

/**
 * Content store file uploader
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ContentStoreFileUploader extends AbstractFileUploader
{
    public function upload($identifier)
    {
        $file = $this->getFile();

        $storeFile = new ContentStoreFile();
        $storeFile->setContent($file->getContent());

        $response = $this->getServiceLocator()->get('ContentStore')->write($identifier, $storeFile);

        if (!$response->isSuccess()) {
            throw new Exception('Unable to store uploaded file: ' . $response->getBody());
        }

        $file->setPath($identifier);
        $file->setIdentifier($identifier);

        return $file;
    }

    /**
     * Download the file
     */
    public function download($identifier)
    {
        $store = $this->getServiceLocator()->get('ContentStore');

        return $store->read($identifier);
    }

    /**
     * Remove the file
     */
    public function remove($identifier)
    {
        $store = $this->getServiceLocator()->get('ContentStore');

        return $store->remove($identifier);
    }
}
