<?php

/**
 * PublicationBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PublicationBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Publication';

    public function handleQuery(QueryInterface $query)
    {
        $publication = $this->getRepo()->fetchUsingId($query);

        return $this->result($publication, $query->getBundle())->serialize();
    }
}
