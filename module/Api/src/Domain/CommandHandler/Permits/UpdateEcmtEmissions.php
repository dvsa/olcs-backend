<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;

use Olcs\Logging\Log\Logger;

/**
 * Update ECMT Euro 6
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class UpdateEcmtEmissions extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';


    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $ecmtApplication EcmtApplication */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtApplication->setEmissions($command->getEmissions());

        Logger::debug("AAAAAAAAAAAAAAAAAAAAAAAAAA" + var_dump($ecmtApplication));

        $this->getRepo()->save($ecmtApplication);
        $result->addId('ecmtEuro6', $ecmtApplication->getId());
        $result->addMessage('ECMT Permit Application Euro6 updated');
        return $result;
    }

}
