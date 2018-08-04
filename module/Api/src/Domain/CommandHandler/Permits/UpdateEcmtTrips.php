<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;

/**
 * Update ECMT Trips
 *
 * @author Andy Newton <andrew.newton@capgemini.com>
 */
final class UpdateEcmtTrips extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';


    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $ecmtApplication EcmtApplication */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtApplication->setTrips($command->getTrips());

        $this->getRepo()->save($ecmtApplication);
        $result->addId('ecmtTrips', $ecmtApplication->getId());
        $result->addMessage('ECMT Permit Application Trips updated');
        return $result;
    }
}
