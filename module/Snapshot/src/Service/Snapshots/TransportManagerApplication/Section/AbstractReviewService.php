<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Snapshot\Service\Formatter\Address;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Format Full name
     *
     * @param Person $person Person Entity
     *
     * @return string
     */
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

    /**
     * Format date
     *
     * @param \DateTime|string $date   Date
     *
     * @return bool|string
     */
    protected function formatDate($date)
    {
        $format = 'd M Y';

        if ($date instanceof \DateTime) {
            return $date->format($format);
        }

        return date($format, strtotime($date));
    }

    /**
     * Format address
     *
     * @param array|\Dvsa\Olcs\Api\Entity\ContactDetails\Address $address Address data or entity
     *
     * @return string
     */
    protected function formatFullAddress($address)
    {
        return Address::format($address, ['addressFields' => 'FULL']);
    }

    /**
     * Format address short
     *
     * @param array|\Dvsa\Olcs\Api\Entity\ContactDetails\Address $address Address data or entity
     *
     * @return string
     */
    protected function formatShortAddress($address)
    {
        return Address::format($address);
    }

    /**
     * Filter documents
     *
     * @param ArrayCollection $files       List of documents
     * @param string          $category    Category
     * @param string          $subCategory Sub category
     *
     * @return mixed
     */
    protected function findFiles($files, $category, $subCategory)
    {
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->in('category', [$category]));
        $criteria->andWhere($criteria->expr()->in('subCategory', [$subCategory]));

        return $files->matching($criteria);
    }

    /**
     * Translate
     *
     * @param string $string Text or Tranlation key
     *
     * @return string
     */
    protected function translate($string)
    {
        return $this->getServiceLocator()->get('translator')->translate($string, 'snapshot');
    }

    /**
     * Translate and replace
     *
     * @param string $translationKey Tranlation key
     * @param array  $arguments      Values for replacement
     *
     * @return string
     */
    protected function translateReplace($translationKey, array $arguments)
    {
        return vsprintf($this->translate($translationKey), $arguments);
    }

    /**
     * Format Yes/No
     *
     * @param string $value Y or N
     *
     * @return string
     */
    protected function formatYesNo($value)
    {
        return $value === 'Y' ? 'Yes' : 'No';
    }
}
