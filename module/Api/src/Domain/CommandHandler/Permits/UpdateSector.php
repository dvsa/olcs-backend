<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\System\RefData;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Jason de Jonge
 */
final class UpdateSector extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['Sectors'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $application = $this->getRepo()->fetchById($command->getId());

        /** @var Repository\Sectors $repo */
        $sectorRepo = $this->getRepo('Sectors');

        if (empty($application)) {
            $result->addMessage('No permit application to update');

            return $result;
        }

        /** @var EcmtPermitApplication $application */
        $application->setSectors($sectorRepo->getRefdataReference($command->getSector()));

        $this->getRepo()->save($application);

        $result->addId('ecmtPermitApplication', $application->getId());

        return $result;
    }
}
