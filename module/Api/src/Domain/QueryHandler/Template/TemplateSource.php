<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Template;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\Template\TemplateSource as TemplateSourceQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Template source
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TemplateSource extends AbstractQueryHandler
{
    protected $repoServiceName = 'Template';

    /**
     * Handle query
     *
     * @param QueryInterface|TemplateSourceQry $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $template = $this->getRepo()->fetchUsingId($query);

        return [
            'source' => $template->getSource(),
            'locale' => $template->getLocale(),
            'format' => $template->getFormat()
        ];
    }
}
