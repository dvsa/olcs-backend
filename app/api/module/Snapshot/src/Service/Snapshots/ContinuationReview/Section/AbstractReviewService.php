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
        return $this->getServiceLocator()->get('translator')->translate($string, 'snapshot');
    }
}
