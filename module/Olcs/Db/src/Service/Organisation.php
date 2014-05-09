<?php

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array();
    }

    /**
     * Gets an organisation record by licenceId.
     * @todo Possibly use join... needs performence review
     *
     * @param int $id
     *
     * @return array
     */
    public function getByLicenceId($id)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $licenceEntity = $this->getEntityManager()->getRepository('OlcsEntities\Entity\Licence')->findOneBy(['id' => $id]);
        if (empty($licenceEntity)) {
            return null;
        }
        $orgEntity = $licenceEntity->getOrganisation();
        if (empty($orgEntity)) {
            return null;
        }

        $data = $this->extract($orgEntity);
        return $data;
    }

    /**
     * Update an organisation record by licenceId.
     *
     * @todo Possibly use join... needs performence review
     *
     * @param int $id
     * @param array $data
     * @throws NoVersionException
     * @return NULL|boolean
     */
    public function updateByLicenceId($id, $data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));
        
        if (!isset($data['version'])) {
            throw new NoVersionException('A version number must be specified to update an entity');
        }

        $licenceEntity = $this->getEntityManager()->getRepository('OlcsEntities\Entity\Licence')->findOneBy(['id' => $id]);
        if (empty($licenceEntity)) {
            return null;
        }

        $orgEntity = $licenceEntity->getOrganisation();
        if (empty($orgEntity)) {
            return null;
        }

        $hydrator = $this->getDoctrineHydrator();
        $orgEntity = $hydrator->hydrate($data, $orgEntity);

        $this->getEntityManager()->lock($orgEntity, LockMode::OPTIMISTIC, $data['version']);

        $this->dbPersist($orgEntity);
        $this->dbFlush();

        return true;
    }

    public function getApplicationsList($data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('a')
            ->from('OlcsEntities\Entity\Application', 'a')
            ->innerJoin('OlcsEntities\Entity\Licence', 'l', 'WITH', 'a.licence = l.id')
            ->add('where', 'l.organisation = :operator')
            ->add('orderBy', 'a.createdOn DESC')
            ->setParameter('operator', $data['organisation'])
            ->getQuery()
        ;

        $results = $query->getResult();

        if (!empty($results)) {

            $rows = array();

            foreach ($results as $row) {

                $rows[] = $this->getBundleCreator()->buildEntityBundle($row, $data);
            }

            $results = $rows;
        }

        return array(
            'Count' => count($results),
            'Results' => $results,
        );
    }

}
