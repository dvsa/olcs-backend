<?php

namespace Dvsa\Olcs\Scanning\Controller;

/**
 * Document controller
 */
class DocumentController extends AbstractController
{
    /**
     * Create action
     *
     * @param array $postData POST data
     *
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\JsonModel
     */
    public function create($postData)
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        $scanId = (int) $request->getPost('description');
        $uploadImage = $request->getFiles()->get('image');
        try {
            $this->validateRequest($scanId, $uploadImage);
        } catch (\Exception $e) {
            return $this->respondError(400, $e->getMessage());
        }

        try {
            $dtoData = [
                'content' => $this->getUploadContents($uploadImage),
                'filename' => $uploadImage['name'],
                'scanId' => $scanId
            ];
            $this->handleCommand(\Dvsa\Olcs\Transfer\Command\Scan\CreateDocument::create($dtoData));
        } catch (\Dvsa\Olcs\Api\Domain\Exception\Exception $e) {
            $messages = $e->getMessages();
            if (isset($messages['SCAN_INVALID_MIME'])) {
                return $this->respondError(415, 'Unsupported Media Type');
            }

            if (isset($messages['SCAN_NOT_FOUND'])) {
                return $this->respondError(400, 'Cannot find scan record');
            }

            $this->logError('Error processing scan document', $messages);
            return $this->respondError(500, 'Internal Server Error');
        } catch (\Exception $e) {
            $this->logError('Error processing scan document', ['message' => $e->getMessage()]);
            return $this->respondError(500, 'Internal Server Error');
        }

        // Everything worked ok
        $this->getResponse()->setStatusCode(204);

        return $this->getResponse();
    }

    /**
     * Validate the request parameters
     *
     * @param int   $scanId      ID od scan record
     * @param array $uploadImage uploaded image data
     *
     * @return void
     */
    protected function validateRequest($scanId, $uploadImage)
    {
        if (!is_numeric($scanId) || ($scanId === 0)) {
            throw new \RuntimeException('POST "description" is not a valid number');
        }

        if ($uploadImage === null) {
            throw new \RuntimeException('POST "image" is missing');
        }

        $validator = new \Zend\Validator\File\UploadFile();
        if (!$validator->isValid($uploadImage)) {
            throw new \RuntimeException(implode(' AND ', $validator->getMessages()));
        }
    }

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $dto DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    private function handleCommand($dto)
    {
        return $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
    }

    /**
     * Get file contents of uploaded file
     *
     * @param array $data Uploaded file data
     *
     * @return string
     */
    protected function getUploadContents(array $data)
    {
        return base64_encode(file_get_contents($data['tmp_name']));
    }
}
