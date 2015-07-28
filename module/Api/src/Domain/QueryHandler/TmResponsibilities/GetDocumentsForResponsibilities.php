<?php

/**
 * Get documents for tm responsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmResponsibilities;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get documents for tm responsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetDocumentsForResponsibilities extends AbstractQueryHandler
{
    protected $repoServiceName = 'Document';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        if ($query->getType() == 'application') {
            $documents = $this->getRepo()
                ->fetchListForTmApplication(
                    $query->getTransportManager(),
                    $query->getLicOrAppId()
                );
        } else {
            $documents = $this->getRepo()
                ->fetchListForTmLicence(
                    $query->getTransportManager(),
                    $query->getLicOrAppId()
                );
        }
        return [
            'result' => $documents,
            'count' => count($documents),
        ];
    }
}
