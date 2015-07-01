<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Licence\Withdraw;
use Dvsa\Olcs\Transfer\Query\LicenceVehicle\LicenceVehicle;

/**
 * Class WithdrawApplication
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Application
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class WithdrawApplication extends AbstractCommandHandler implements TransactionedInterface
{
    public $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_WITHDRAWN));
        $application->setWithdrawnDate(new \DateTime());
        $application->setWithdrawnReason($this->getRepo()->getRefdataReference($command->getReason()));

        $this->getRepo()->save($application);

        $result = new Result();

        if ($application->getIsVariation() === false) {
            $result->merge(
                $this->handleSideEffect(
                    Withdraw::create(
                        [
                            'id' => $application->getLicence()->getId()
                        ]
                    )
                )
            );
        }

        $result->merge(
            $this->handleSideEffect(
                CeaseGoodsDiscs::create(
                    [
                        'licence' => $application->getLicence()
                    ]
                )
            )
        );

        $result->addMessage('Application ' . $application->getId() . ' withdrawn.');

        return $result;
    }
}
