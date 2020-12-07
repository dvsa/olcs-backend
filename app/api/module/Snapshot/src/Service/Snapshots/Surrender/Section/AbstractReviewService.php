<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\FormatReviewDataTrait;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractReviewService implements ReviewServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, FormatReviewDataTrait;
}
