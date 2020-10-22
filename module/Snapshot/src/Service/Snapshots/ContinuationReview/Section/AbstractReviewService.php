<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Translate
     *
     * @param string $string text or translation key
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->getServiceLocator()->get('translator')->translate($string);
    }

    /**
     * Translate and replace parameters
     *
     * @param string $translationKey Message key to translate
     * @param array  $arguments      Items to be replaced in
     *
     * @return string
     */
    protected function translateReplace($translationKey, array $arguments)
    {
        return vsprintf($this->translate($translationKey), $arguments);
    }

    /**
     * Format a date
     *
     * @param string $date   Date to format
     * @param string $format Date format eg "d M Y"
     *
     * @return string Formatted date
     */
    public function formatDate($date, $format = 'd M Y')
    {
        return date($format, strtotime($date));
    }
}
