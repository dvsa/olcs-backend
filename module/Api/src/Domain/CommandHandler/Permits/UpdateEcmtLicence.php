<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Update ECMT Application Licence
 *
 * @author Andy Newton
 */
final class UpdateEcmtLicence extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['Licence'];

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


        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());


        if (empty($application)) {
            $result->addMessage('No permit application to update');
            return $result;
        }

        $application->setLicence($licence);
        $this->getRepo()->save($application);
        $result->addId('ecmtPermitApplication', $application->getId());
        $result->addMessage('EcmtPermitApplication Licence Updated successfully');

        return $result;
    }
}
