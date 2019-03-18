<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Template\Template as Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Doctrine\ORM\NoResultException;

/**
 * Template
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class Template extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch by locale, format and name
     *
     * @param string $locale
     * @param string $format
     * @param string $name
     *
     * @return Entity
     */
    public function fetchByLocaleFormatName($locale, $format, $name)
    {
        try {
            return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from(Entity::class, 't')
                ->where('t.locale = ?1')
                ->andWhere('t.format = ?2')
                ->andWhere('t.name = ?3')
                ->setParameter(1, $locale)
                ->setParameter(2, $format)
                ->setParameter(3, $name)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new NotFoundException('Resource not found');
        }
    }

    /**
     * Fetch all
     *
     * @return array
     */
    public function fetchAll()
    {
        $qb = $this->createQueryBuilder();
        return $qb->getQuery()->getResult();
    }
}
