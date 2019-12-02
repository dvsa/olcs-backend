<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

/**
 * Update ECMT No Of Permits
 *
 * @author Andy Newton <andrew.newton@capgemini.com>
 */
final class UpdateEcmtPermitsRequired extends AbstractCommandHandler implements ToggleRequiredInterface, TransactionedInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleCommand(CommandInterface $command)
    {
        /* @var EcmtPermitApplication $ecmtApplication */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $licence = $ecmtApplication->getLicence();

        $newTotalRequired = $command->getRequiredEuro5() + $command->getRequiredEuro6();

        try {
            $totalPermitsRequired = $ecmtApplication->calculateTotalPermitsRequired();
        } catch (RuntimeException $e) {
            $totalPermitsRequired = 0;
        }

        if ($totalPermitsRequired !== $newTotalRequired) {
            $this->result->merge($this->handleSideEffect(
                UpdatePermitFee::create(
                    [
                        'ecmtPermitApplicationId' => $ecmtApplication->getId(),
                        'licenceId' => $licence->getId(),
                        'permitsRequired' => $newTotalRequired,
                        'permitType' => $ecmtApplication::PERMIT_TYPE,
                        'receivedDate' => $ecmtApplication->getDateReceived()
                    ]
                )
            ));
        }

        $ecmtApplication->updatePermitsRequired($command->getRequiredEuro5(), $command->getRequiredEuro6());

        $this->getRepo()->save($ecmtApplication);

        $this->result->addId('ecmtPermitsRequired', $ecmtApplication->getId());
        $this->result->addMessage('ECMT Permit Application Permits Required updated');

        return $this->result;
    }
}
