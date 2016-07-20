<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;

/**
 * Download a guide document, these are located in a "/guides/" directory in the content store
 */
class DownloadGuide extends AbstractQueryHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Document\DownloadGuide $query DTO
     *
     * @return array
     * @throws NotFoundException
     */
    public function handleQuery(QueryInterface $query)
    {
        // make sure that the file identifier cannot go up directory structure to secure documents
        if (strpos($query->getIdentifier(), '..') !== false) {
            throw new NotFoundException();
        }

        $filePath = '/guides/'. $query->getIdentifier();
        $file = $this->getUploader()->download($filePath);
        if ($file === null) {
            throw new NotFoundException();
        }

        return [
            'fileName' => $query->getIdentifier(),
            'content' => base64_encode($file->getContent()),
        ];
    }
}
