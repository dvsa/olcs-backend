<?php
/**
 * Note
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteHistoryEntity;
use Dvsa\Olcs\Transfer\Query\Processing\NoteList as NoteDTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CaseRepository;
use Doctrine\ORM\Query;

/**
 * Note
 */
class Note extends AbstractRepository
{
    protected $entity = NoteHistoryEntity::class;

    protected $alias = 'n';

    /**
     * @param QueryBuilder $qb
     * @param NoteDTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        /** @var \Dvsa\Olcs\Transfer\Query\Processing\NoteList $query */

        if ($query->getCase() !== null) {
            $qb->andWhere($this->alias . '.case = :caseId');
            $qb->setParameter('caseId', $query->getCase());
        }

        if ($query->getCasesMultiple() !== null && count($query->getCasesMultiple()) > 0) {
            $qb->andWhere($this->alias . '.case IN (:casesMultiple)');
            $qb->setParameter('casesMultiple', $query->getCasesMultiple());
        }

        if ($query->getLicence() !== null) {
            $qb->andWhere($this->alias . '.licence = :licenceId');
            $qb->setParameter('licenceId', $query->getLicence());
        }

        if ($query->getOrganisation() !== null) {
            $qb->andWhere($this->alias . '.organisation = :organisationId');
            $qb->setParameter('organisationId', $query->getOrganisation());
        }

        if ($query->getTransportManager() !== null) {
            $qb->andWhere($this->alias . '.transportManager = :transportManagerId');
            $qb->setParameter('transportManagerId', $query->getTransportManager());
        }

        if ($query->getUser() !== null) {
            $qb->andWhere($this->alias . '.createdBy = :userId');
            $qb->setParameter('userId', $query->getUser());
        }

        if ($query->getApplication() !== null) {
            $qb->andWhere($this->alias . '.application = :applicationId');
            $qb->setParameter('applicationId', $query->getApplication());
        }

        if ($query->getNoteType() !== null) {
            $qb->andWhere($this->alias . '.noteType = :noteTypeId');
            $qb->setParameter('noteTypeId', $query->getNoteType());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->withCreatedBy()->withBusReg()->withCase();
    }

    /**
     * Fetch a list for an organisation
     *
     * @param int|Organisation $organisation
     *
     * @return array
     */
    public function fetchByOrganisation($organisation)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.organisation', ':organisaion'))
            ->setParameter('organisaion', $organisation);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch a list for a Transport Manager
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Tm\TransportManager $transportManager
     *
     * @return array
     */
    public function fetchByTransportManager($transportManager)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.transportManager', ':transportManager'))
            ->setParameter('transportManager', $transportManager);

        return $doctrineQb->getQuery()->getResult();
    }
}
