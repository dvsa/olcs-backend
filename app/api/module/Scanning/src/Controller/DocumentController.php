<?php

/**
 * Document controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Scanning\Controller;

use Dvsa\Olcs\Scanning\Service\ScanningService;

/**
 * Document controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentController extends AbstractController
{
    public function create($postData)
    {
        $scanner = $this->getScanningService();

        $this->debug('Validating request...');

        if (!$scanner->isValidRequest()) {
            return $this->respondError(400, 'Bad Request');
        }

        $data = $scanner->getData();
        $scanId = (int) $data['description'];

        $dtoData = [
            'content' => $this->getUploadContents($data),
            'filename' => $data['image']['name'],
            'scanId' => $scanId
        ];

        try {
            $this->handleCommand(\Dvsa\Olcs\Transfer\Command\Scan\CreateDocument::create($dtoData));
        } catch (\Exception $e) {
            return $this->handleNotOkResponse($scanId, $e);
        }

        $this->getResponse()->setStatusCode(204);

        return $this->getResponse();
    }

    protected function handleNotOkResponse($scanId, \Exception $e)
    {
        if ($e instanceof \Dvsa\Olcs\Api\Domain\Exception\Exception) {
            $messages = $e->getMessages();
        } else {
            $messages = $e->getMessage();
        }

        if (isset($messages['SCAN_INVALID_MIME'])) {
            $this->debug('Invalid mime type: ' . $messages['SCAN_INVALID_MIME']);
            return $this->respondError(415, 'Unsupported Media Type');
        }

        if (isset($messages['SCAN_NOT_FOUND'])) {
            $this->debug('Scan record with ID ' . $scanId . ' does not exist');
            return $this->respondError(400, 'Cannot find scan record');
        }

        $this->debug(
            'Error executing backend Scan\CreateDocument command :'. $messages
        );

        return $this->respondError(500, 'Internal Server Error');
    }

    /**
     * @return ScanningService
     */
    protected function getScanningService()
    {
        $scanner = $this->getServiceLocator()->get('Scanning');
        $scanner->setDataFromRequest($this->getRequest());

        return $scanner;
    }

    /**
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $dto
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    private function handleCommand($dto)
    {
        return $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
    }

    protected function getUploadContents($data)
    {
        return base64_encode(file_get_contents($data['image']['tmp_name']));
    }
}
