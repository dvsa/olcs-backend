<?php

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Snapshot\Service\Formatter\Address;

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected function formatPersonFullName(Person $person)
    {
        $parts = [];

        if ($person->getTitle() !== null) {
            $parts[] = $person->getTitle()->getDescription();
        }

        $parts[] = $person->getForename();
        $parts[] = $person->getFamilyName();

        return implode(' ', $parts);
    }

    protected function formatDate($date, $format = 'd F Y')
    {
        if ($date instanceof \DateTime) {
            return $date->format($format);
        }

        return date($format, strtotime($date));
    }

    protected function formatFullAddress($address)
    {
        return Address::format($address, ['addressFields' => 'FULL']);
    }

    protected function formatShortAddress($address)
    {
        return Address::format($address);
    }

    protected function findFiles($files, $category, $subCategory)
    {
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->in('category', [$category]));
        $criteria->andWhere($criteria->expr()->in('subCategory', [$subCategory]));

        return $files->matching($criteria);
    }

    protected function translate($string)
    {
        return $this->getServiceLocator()->get('translator')->translate($string, 'snapshot');
    }

    protected function formatYesNo($value)
    {
        return $value === 'Y' ? 'Yes' : 'No';
    }
}
