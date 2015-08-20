<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'o';

    public function fetchBusinessDetailsUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->fetchBusinessDetailsById($query->getId(), $hydrateMode);
    }

    public function fetchBusinessDetailsById($id, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id)->withContactDetails();

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new NotFoundException('Organisation not found');
        }

        return $results[0];
    }

    public function fetchIrfoDetailsUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->fetchIrfoDetailsById($query->getId(), $hydrateMode);
    }

    public function fetchIrfoDetailsById($id, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('irfoNationality')
            ->with('irfoPartners')
            ->with('tradingNames', 'tn')
            ->withContactDetails('irfoContactDetails')
            ->byId($id);

        // get only trading names which are not linked to a licence
        $qb->andWhere($qb->expr()->isNull('tn.licence'));

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    public function getByCompanyOrLlpNo($companyNumber)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata();

        $qb
            ->andWhere($qb->expr()->eq($this->alias . '.companyOrLlpNo', ':companyNumber'))
            ->setParameter('companyNumber', $companyNumber);

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            throw new NotFoundException('Organisation not found for company number '.$companyNumber);
        }

        return $results;
    }

    public function fetchByStatusPaginated($query)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata();

        if ($query->getCpid() !== Entity::OPERATOR_CPID_ALL) {
            if (is_null($query->getCpid())) {
                $qb->where($qb->expr()->isNull($this->alias . '.cpid'));
            } else {
                $status = $this->getRefdataReference($query->getCpid());
                $qb->where($qb->expr()->eq($this->alias . '.cpid', ':cpid'));
                $qb->setParameter(
                    'cpid', $status
                );
            }
        }

        $qb->setFirstResult(($query->getLimit() * $query->getPage()) - $query->getLimit());
        $qb->setMaxResults($query->getLimit());
        $qb->addOrderBy($this->alias . '.name', 'ASC');

        return [
            'result' => $this->fetchPaginatedList($qb, Query::HYDRATE_OBJECT),
            'count' => $this->fetchPaginatedCount($qb)
        ];
    }

    public function fetchAllByStatusForCpidExport($status = null)
    {
        $qb = $this->createQueryBuilder('o');

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('cpid', 'r');

        if ($status !== Entity::OPERATOR_CPID_ALL) {
            if (is_null($status)) {
                $qb->where($qb->expr()->isNull($this->alias . '.cpid'));
            } else {
                $qb->where($qb->expr()->eq($this->alias . '.cpid', ':cpid'));
                $qb->setParameter('cpid', $status);
            }
        }

        $qb->select(
            $this->alias . '.id',
            $this->alias . '.name',
            'r.id AS cpid'
        );

        $query = $qb->getQuery();

        return $query->iterate(null, Query::HYDRATE_ARRAY);
    }
}
