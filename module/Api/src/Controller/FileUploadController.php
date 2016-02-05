<?php

namespace Dvsa\Olcs\Api\Controller;

use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * File Upload Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @method \Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response response()
 */
class FileUploadController extends AbstractRestfulController
{
    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $files = (array)$this->getRequest()->getFiles();

        if (isset($files['file']) && empty($files['file']['error'])) {

            /** @var ContentStoreFileUploader $uploader */
            $uploader = $this->getServiceLocator()->get('FileUploader');

            // Upload the file
            $file = [
                'name' => basename($files['file']['tmp_name']),
                'content' => file_get_contents($files['file']['tmp_name']),
                'size' => $files['file']['size']
            ];

            try {
                $uploader->setFile($file);
                $file = $uploader->upload('tmp/' . basename($files['file']['tmp_name']));
            } catch (MimeNotAllowedException $ex) {
                return $this->response()->error(400, ['message' => 'Invalid mime']);
            }

            return $this->response()->successfulCreate(['identifier' => $file->getIdentifier()]);
        }

        if (isset($files['file'])) {
            return $this->response()->error(400, ['error' => $files['file']['error'], 'message' => 'Upload error']);
        }

        return $this->response()->error(400, ['message' => 'No file received']);
    }
}
