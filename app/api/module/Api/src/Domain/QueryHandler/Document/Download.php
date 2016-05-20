<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
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
     * @param \Dvsa\Olcs\Transfer\Query\Document\Download $query
     * @return array
     * @throws NotFoundException
     */
    public function handleQuery(QueryInterface $query)
    {
        $file = $this->getUploader()->download($query->getIdentifier());

        if ($file === null) {
            throw new NotFoundException();
        }

        return [
            'fileName' => basename($query->getIdentifier()),
            'content' => base64_encode($file->getContent()),
        ];
    }
}
