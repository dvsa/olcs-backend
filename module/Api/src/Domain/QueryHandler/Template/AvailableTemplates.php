<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Template;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\Template\AvailableTemplates as AvailableTemplatesQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Available templates
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AvailableTemplates extends AbstractQueryHandler
{
    protected $repoServiceName = 'Template';

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableTemplatesQry $query query
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
        $templates = $this->getRepo()->fetchAll();

        $responseItems = [];
        foreach ($templates as $template) {
            $responseItems[] = [
                'id' => $template->getId(),
                'locale' => $template->getLocale(),
                'format' => $template->getFormat(),
                'description' => $template->getDescription(),
                'category' => $template->getComputedCategoryName()
            ];
        }

        return $responseItems;
    }
}
