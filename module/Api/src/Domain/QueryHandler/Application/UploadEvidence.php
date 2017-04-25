<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity;

/**
 * Upload Evidence
 */
class UploadEvidence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Application\UploadEvidence $query Query DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo()->fetchById($query->getId());

        $financialEvidenceDocuments = $application->getApplicationDocuments(
            $this->getRepo()->getCategoryReference(Entity\System\Category::CATEGORY_APPLICATION),
            $this->getRepo()->getSubCategoryReference(
                Entity\System\SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
        );

        return [
            'financialEvidence' => [
                'canAdd' => $application->canAddFinancialEvidence(),
                'documents' => $this->resultList($financialEvidenceDocuments),
            ],
            'operatingCentres' => [
            ],
        ];
    }
}
