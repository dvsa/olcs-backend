<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermitApplication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */

    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermitApplication = $this->createPermitApplicationObject($command);

        $this->getRepo()->save($ecmtPermitApplication);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('EcmtPermitApplication created successfully');

        return $result;
    }

    /**
     * Create EcmtPermitApplication object
     *
     * @param Cmd $command Command
     *
     * @return EcmtPermitApplication
     */
    private function createPermitApplicationObject($command)
    {
        return EcmtPermitApplication::createNew(
            $this->getRepo()->getRefdataReference($command->getStatus()),
            $this->getRepo()->getRefdataReference($command->getPaymentStatus()),
            $this->getRepo()->getRefdataReference($command->getPermitType()),
            $command->getLicence()
        );
    }
}
