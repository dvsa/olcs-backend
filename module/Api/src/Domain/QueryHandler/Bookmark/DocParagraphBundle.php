<?php

/**
 * DocParagraphBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * DocParagraphBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocParagraphBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'DocParagraph';

    public function handleQuery(QueryInterface $query)
    {
        $docParagraph = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $docParagraph,
            $query->getBundle()
        )->serialize();
    }
}
