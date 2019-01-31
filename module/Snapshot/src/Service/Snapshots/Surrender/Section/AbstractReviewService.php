<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\FormatReviewDataTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, FormatReviewDataTrait;
}
