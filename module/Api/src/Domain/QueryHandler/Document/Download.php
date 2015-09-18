<?php

/**
 * Download
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\Document\Download as Qry;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;

/**
 * Download
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Download extends AbstractQueryHandler
{
    protected $repoServiceName = 'Document';

    /**
     * @var ContentStoreFileUploader
     */
    protected $fileUploader;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileUploader = $mainServiceLocator->get('FileUploader');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Qry $query
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var DocumentEntity[] $documents */
        $documents = $this->getRepo()->fetchByIdentifier($query->getIdentifier());

        if (empty($documents)) {
            throw new NotFoundException();
        }

        $document = $documents[0];

        $fullFileName = $document->getFilename();
        $parts = explode('/', $fullFileName);
        $fileName = array_pop($parts);

        $file = $this->fileUploader->download($document->getIdentifier());

        if ($file === null) {
            throw new NotFoundException();
        }

        return $this->result(
            $document,
            [],
            [
                'fileName' => $fileName,
                'content' => base64_encode($file->getContent())
            ]
        );
    }
}
