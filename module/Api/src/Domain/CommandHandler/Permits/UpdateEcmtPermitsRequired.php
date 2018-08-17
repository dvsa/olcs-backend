<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;

/**
 * Update ECMT Euro 6
 *
 * @author Andy Newton <andrew.newton@capgemini.com>
 */
final class UpdateEcmtPermitsRequired extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $ecmtApplication EcmtApplication */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtApplication->updatePermitsRequired($command->getPermitsRequired());

        $this->getRepo()->save($ecmtApplication);

        $result->addId('ecmtPermitsRequired', $ecmtApplication->getId());
        $result->addMessage('ECMT Permit Application Permits Required updated');

        return $result;
    }
}
