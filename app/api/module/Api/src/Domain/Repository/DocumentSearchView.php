<?php

/**
 * Document Search View
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\View\DocumentSearchView as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Document Search View
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentSearchView extends AbstractReadonlyRepository
{
    /**
     * Setting to false removes the unnecessary DISTINCT clause from pagination queries
     * @see http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/tutorials/pagination.html
     *
     * @var bool
     */
    protected $fetchJoinCollection = false;

    protected $entity = Entity::class;

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getIsExternal() !== null) {
            $qb->andWhere(
                $qb->expr()->eq('m.isExternal', ':isExternal')
            );
            $qb->setParameter('isExternal', $query->getIsExternal() == 'Y' ? 1 : 0);
        }

        if ($query->getCategory() !== null) {
            $qb->andWhere(
                $qb->expr()->eq('m.category', $query->getCategory())
            );
        }

        if (!empty($query->getDocumentSubCategory())) {
            $qb->andWhere(
                $qb->expr()->in('m.documentSubCategory', $query->getDocumentSubCategory())
            );
        }

        $idExpressions = [];

        if ($query->getLicence() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.licenceId', ':licence'
            );
            $qb->setParameter('licence', $query->getLicence());
        }

        if ($query->getTransportManager() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.tmId', ':tm'
            );
            $qb->setParameter('tm', $query->getTransportManager());
        }

        if ($query->getCase() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.caseId', ':case'
            );
            $qb->setParameter('case', $query->getCase());
        }

        if ($query->getIrfoOrganisation() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.irfoOrganisationId', ':irfoOrganisation'
            );
            $qb->setParameter('irfoOrganisation', $query->getIrfoOrganisation());
        }

        if (!empty($idExpressions)) {
            $expr = $qb->expr();

            $qb->andWhere(
                call_user_func_array([$expr, 'orX'], $idExpressions)
            );
        }
    }
}
