<?php

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Zend\Stdlib\ArraySerializableInterface;
use Doctrine\ORM\Query;

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicence extends AbstractCommandHandler
{
    public function handleCommand(ArraySerializableInterface $command)
    {
        /** @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        if ($application === null) {
            return null;
        }

        $application->updateTypeOfLicence(
            $command->getNiFlag(),
            $this->getRepo()->getRefdataReference($command->getOperatorType()),
            $this->getRepo()->getRefdataReference($command->getLicenceType()),
            $command->getConfirm()
        );

        print_r($command);
        exit;
    }
}
