<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\QueryHandler\Document\AbstractDownload;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stub class of AbstractDownload handler for testing
 */
class AbstractDownloadStub extends AbstractDownload
{
    public function download($identifier, $path = null)
    {
        return parent::download($identifier, $path);
    }

    public function setIsInline($inline)
    {
        return parent::setIsInline($inline);
    }

    public function handleQuery(QueryInterface $query)
    {
        // suppress codesniffer warning
        unset($query);
    }
}
