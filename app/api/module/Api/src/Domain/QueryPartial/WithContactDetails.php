<?php

/**
 * With Contact Details
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;

/**
 * With Contact Details
 */
final class WithContactDetails implements QueryPartialInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var With
     */
    private $with;

    /**
     * @var WithRefdata
     */
    private $withRefdata;

    public function __construct(EntityManagerInterface $em, With $with, WithRefdata $withRefdata)
    {
        $this->em = $em;
        $this->with = $with;
        $this->withRefdata = $withRefdata;
    }

    /**
     * Grabs a contact details relationship with address and refdata
     * $property is the name of the ContactDetails property as references from it's parent entity e.g. m.contactDetails
     * $alias is the alias to give the contact details object
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        switch (count($arguments)) {
            case 2:
                list($property, $alias) = $arguments;
                break;
            case 1:
                $property = $arguments[0];
                $alias = $qb->getRootAliases()[0] . '_cd';
                break;
            default:
                $rootAlias = $qb->getRootAliases()[0];

                $property = $rootAlias . '.contactDetails';
                $alias = $rootAlias . '_cd';
                break;
        }

        if (!strstr($property, '.')) {
            $property = $qb->getRootAliases()[0] . '.' . $property;
        }

        $this->with->modifyQuery($qb, [$property, $alias]);
        $this->with->modifyQuery($qb, [$alias . '.address', $alias . '_a']);
        $this->with->modifyQuery($qb, [$alias . '_a.countryCode', $alias . '_a_cc']);

        $this->withRefdata->modifyQuery($qb, [ContactDetails::class, $alias]);
        $this->withRefdata->modifyQuery($qb, [Address::class, $alias . '_a']);
    }
}
