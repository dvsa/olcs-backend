<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Download a guide document, these are located in a "/guides/" directory in the content store
 */
class DownloadGuide extends AbstractDownload
{
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
        $this->setIsInline($query->isInline());

        $identifier = $query->getIdentifier();

        // make sure that the file identifier cannot go up directory structure to secure documents
        if (strpos($identifier, '..') !== false) {
            throw new NotFoundException();
        }

        return $this->download($identifier, '/guides/' . $identifier);
    }
}
