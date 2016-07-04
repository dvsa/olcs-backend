<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Licence extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByCaseId($caseId, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->with('trafficArea', 'ta');
        $qb->innerJoin($this->alias . '.cases', 'c');

        $qb->andWhere($qb->expr()->eq('c.id', ':caseId'));
        $qb->setParameter('caseId', $caseId);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }

    public function fetchWithAddressesUsingId($query)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $query->getId())
            ->withContactDetails('correspondenceCd', 'c')
            ->with('c.phoneContacts', 'c_p')
            ->with('c_p.phoneContactType', 'c_p_pct')
            ->withRefData(PhoneContact::class, 'c_p')
            ->withContactDetails('establishmentCd', 'e')
            ->withContactDetails('transportConsultantCd', 't')
            ->with('t.phoneContacts', 't_p')
            ->with('t_p.phoneContactType', 't_p_pct')
            ->withRefData(PhoneContact::class, 't_p');

        return $qb->getQuery()->getSingleResult();
    }

    public function fetchSafetyDetailsUsingId($command, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        return $this->fetchSafetyDetailsById($command->getId(), $hydrateMode, $version);
    }

    public function fetchSafetyDetailsById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id)
            ->with('workshops', 'w')
            ->withContactDetails('w.contactDetails');

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }

    /**
     * Get a Licence by the LicNo
     *
     * @param string $licNo
     * @throws Exception\NotFoundException
     *
     * @return Entity
     */
    public function fetchByLicNo($licNo)
    {
        $dqb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($dqb)->withRefdata()
            ->with('operatingCentres', 'ocs')
            ->with('ocs.operatingCentre', 'ocs_oc')
            ->with('ocs_oc.address', 'ocs_oc_a');

        $dqb->where($dqb->expr()->eq($this->alias .'.licNo', ':licNo'))
            ->setParameter('licNo', $licNo);

        $results = $dqb->getQuery()->getResult();

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        return $results[0];
    }

    /**
     * Returns whether or not the licence number exists in the database
     *
     * @param string $licNo
     * @return bool
     */
    public function existsByLicNo($licNo)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias .'.licNo', ':licNo'));
        $qb->setParameter('licNo', $licNo);
        $qb->setMaxResults(1);

        return count($qb->getQuery()->getResult()) === 1;
    }

    /**
     * Returns whether or not the licence number exists in the database
     *
     * @param string $licNo
     * @throws Exception\NotFoundException
     *
     * @return Entity
     */
    public function fetchByLicNoWithoutAdditionalData($licNo)
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq($this->alias .'.licNo', ':licNo'));
        $qb->setParameter('licNo', $licNo);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            throw new Exception\NotFoundException('Licence not found');
        }

        return $result;
    }

    public function fetchForUserRegistration($licNo)
    {
        $licence = $this->fetchByLicNo($licNo);

        // check if it has a correspondence address
        if (!($licence->getCorrespondenceCd() instanceof ContactDetailsEntity)
            || !($licence->getCorrespondenceCd()->getAddress() instanceof AddressEntity)
            || $licence->getCorrespondenceCd()->getAddress()->isEmpty()
        ) {
            throw new Exception\ValidationException(
                [
                    'licenceNumber' => [
                        'ERR_ADDRESS_NOT_FOUND'
                    ]
                ]
            );
        }

        // check if the org is not unlicenced
        if ($licence->getOrganisation()->getIsUnlicensed()) {
            throw new Exception\ValidationException(
                [
                    'licenceNumber' => [
                        'ERR_UNLICENCED_ORG'
                    ]
                ]
            );
        }

        // check if the org has any admin users already
        if (!$licence->getOrganisation()->getAdminOrganisationUsers()->isEmpty()) {
            throw new Exception\ValidationException(
                [
                    'licenceNumber' => [
                        'ERR_ADMIN_EXISTS'
                    ]
                ]
            );
        }

        return $licence;
    }

    public function fetchByVrm($vrm)
    {
        $qb = $this->createQueryBuilder();

        $qb->innerJoin('m.licenceVehicles', 'lv')
            ->innerJoin('lv.vehicle', 'v')
            ->andWhere($qb->expr()->isNull('lv.removalDate'))
            ->andWhere($qb->expr()->eq('v.vrm', ':vrm'))
            ->setParameter('vrm', $vrm);

        $query = $qb->getQuery();

        $query->execute();

        return $query->getResult();
    }

    public function fetchWithEnforcementArea($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('enforcementArea')
            ->byId($licenceId);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function fetchWithOperatingCentres($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->with('operatingCentres', 'oc')
            ->with('oc.operatingCentre', 'oc_oc')
            ->with('oc_oc.address', 'oc_oc_a')
            ->byId($licenceId);

        return $qb->getQuery()->getSingleResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Get a Licence and PrivateHireLicence data
     *
     * @param int $licenceId
     *
     * @return Entity
     */
    public function fetchWithPrivateHireLicence($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('privateHireLicences', 'phl')
            ->with('phl.contactDetails', 'cd')
            ->with('cd.address', 'add')
            ->with('add.countryCode')
            ->byId($licenceId);

        return $qb->getQuery()->getSingleResult(Query::HYDRATE_OBJECT);
    }

    /**
     * Fetch for continuation not sought
     *
     * @param \DateTime|null $now
     * @param int|null       $limit
     *
     * @return array
     */
    public function fetchForContinuationNotSought(\DateTime $now = null, $limit = null)
    {
        if (is_null($now)) {
            $now = new DateTime('now');
        }

        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->with('licenceVehicles', 'lv')
            ->with('lv.goodsDiscs', 'gd')
            ->with('psvDiscs', 'pd')
            ->with('trafficArea', 'ta');

        $qb
            // the continuation date is in the past;
            ->andWhere($qb->expr()->lt($this->alias . '.expiryDate', ':now'))
            // the status of the licence is valid, valid curtailed or valid suspended;
            ->andWhere($qb->expr()->in($this->alias . '.status', ':statuses'))
            // the licence is a goods licence or a PSV special restricted
            // (i.e. it excludes restricted and standard PSV licences)
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq($this->alias .'.goodsOrPsv', ':gv'),
                    $qb->expr()->andX(
                        $qb->expr()->eq($this->alias .'.goodsOrPsv', ':psv'),
                        $qb->expr()->eq($this->alias .'.licenceType', ':sr')
                    )
                )
            )
            // there is an outstanding continuation fee
            ->innerJoin($this->alias . '.fees', 'f')
            ->innerJoin('f.feeType', 'ft')
            ->andWhere($qb->expr()->eq('f.feeStatus', ':feeStatus'))
            ->andWhere($qb->expr()->eq('ft.feeType', ':feeType'))
            ->setParameter('now', $now)
            ->setParameter(
                'statuses',
                [
                    Entity::LICENCE_STATUS_VALID,
                    Entity::LICENCE_STATUS_CURTAILED,
                    Entity::LICENCE_STATUS_SUSPENDED,
                ]
            )
            ->setParameter('feeStatus', FeeEntity::STATUS_OUTSTANDING)
            ->setParameter('feeType', FeeTypeEntity::FEE_TYPE_CONT)
            ->setParameter('gv', Entity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->setParameter('psv', Entity::LICENCE_CATEGORY_PSV)
            ->setParameter('sr', Entity::LICENCE_TYPE_SPECIAL_RESTRICTED);

        // The query can generate a lot of data which can exceed PHP memory_limit
        // minimize this by only selecting the root entity
        $qb->select($this->alias, 'ta');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();
        $results = $query->getResult(Query::HYDRATE_ARRAY);
        $licences = [];
        foreach ($results as $result) {
            $licences[] = [
                'id' => $result['id'],
                'version' => $result['version'],
                'licNo' => $result['licNo'],
                'taName' => $result['trafficArea']['name']
            ];
        }
        return $licences;
    }

    /**
     * Override parent
     *
     * @param QueryBuilder $qb
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, \Dvsa\Olcs\Transfer\Query\QueryInterface $query)
    {
        if (is_numeric($query->getOrganisation())) {
            $qb->andWhere($qb->expr()->eq($this->alias .'.organisation', ':organisation'))
                ->setParameter('organisation', $query->getOrganisation());
        }

        if (!empty($query->getExcludeStatuses())) {
            $qb->andWhere($qb->expr()->notIn($this->alias .'.status', ':excludeStatuses'))
                ->setParameter('excludeStatuses', $query->getExcludeStatuses());
        }
    }

    public function fetchForContinuation($year, $month, $trafficArea)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('trafficArea', 'ta');

        $startDate = new \DateTime($year . '-' . $month . '-01');
        $endDate = new \DateTime($year . '-' . $month . '-01');
        $endDate->modify('last day of this month');

        $qb->andWhere($qb->expr()->gte($this->alias . '.expiryDate', ':expiryFrom'))
            ->setParameter('expiryFrom', $startDate);
        $qb->andWhere($qb->expr()->lte($this->alias . '.expiryDate', ':expiryTo'))
            ->setParameter('expiryTo', $endDate);
        $qb->andWhere($qb->expr()->eq('ta.id', ':trafficArea'))
            ->setParameter('trafficArea', $trafficArea);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function fetchWithVariationsAndInterimInforce($licenceId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('applications', 'a')
            ->with('a.interimStatus', 'ais')
            ->byId($licenceId);
        $qb->andWhere($qb->expr()->eq('a.isVariation', true));
        $qb->andWhere($qb->expr()->eq('a.status', ':applicationStatus'));
        $qb->andWhere($qb->expr()->eq('a.interimStatus', ':interimStatus'));
        $qb->setParameter('applicationStatus', ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION);
        $qb->setParameter('interimStatus', ApplicationEntity::INTERIM_STATUS_INFORCE);

        return $qb->getQuery()->getResult();
    }
}
