<?php

/**
 * Update Licence History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Licence History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UpdateLicenceHistory extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $application->setPrevHasLicence(
            $command->getPrevHasLicence()
        );
        $application->setPrevHadLicence(
            $command->getPrevHadLicence()
        );
        $application->setPrevBeenRefused(
            $command->getPrevBeenRefused()
        );
        $application->setPrevBeenRevoked(
            $command->getPrevBeenRevoked()
        );
        $application->setPrevBeenAtPi(
            $command->getPrevBeenAtPi()
        );
        $application->setPrevBeenDisqualifiedTc(
            $command->getPrevBeenDisqualifiedTc()
        );
        $application->setPrevPurchasedAssets(
            $command->getPrevPurchasedAssets()
        );

        $this->getRepo()->save($application);
        $result->addMessage('Licence history section has been updated');
        return $result;
    }
}
