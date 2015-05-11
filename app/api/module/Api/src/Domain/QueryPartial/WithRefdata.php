<?php

/**
 * With Refdata
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * With Refdata
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class WithRefdata implements QueryPartialInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $refDataEntity = 'Olcs\\Db\\Entity\\RefData';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Joins on all refdata relationships
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        list($entity) = $qb->getRootEntities();
        list($alias) = $qb->getRootAliases();

        /** @var $meta \Doctrine\ORM\Mapping\ClassMetadata */
        $meta = $this->em->getClassMetadata($entity);

        $i = 0;
        foreach($meta->associationMappings as $property => $config) {
            if ($config['targetEntity'] === $this->refDataEntity) {
                $qb->leftJoin($alias . '.' . $property, 'rd' . $i);
                $qb->addSelect('rd' . $i);
                $i++;
            }
        }
    }
}
