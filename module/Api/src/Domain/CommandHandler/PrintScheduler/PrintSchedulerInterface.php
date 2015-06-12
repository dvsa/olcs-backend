<?php

/**
 * Print Scheduler Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Print Scheduler Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface PrintSchedulerInterface extends ServiceLocatorAwareInterface
{
    CONST OPTION_DOUBLE_SIDED = 1;

    public function enqueueFile($fileId, $jobName, $options = []);
}
