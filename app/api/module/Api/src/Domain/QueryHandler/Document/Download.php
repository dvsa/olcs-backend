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
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;

/**
 * Download
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Download extends AbstractQueryHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'Document';

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
        $fileName = basename($fullFileName);

        $file = $this->getUploader()->download($document->getIdentifier());

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
