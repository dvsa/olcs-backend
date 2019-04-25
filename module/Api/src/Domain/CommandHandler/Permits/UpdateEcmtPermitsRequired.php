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
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;

/**
 * Update ECMT Euro 6
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

        if ((int)$ecmtApplication->getPermitsRequired() !== (int)$command->getPermitsRequired()) {
            $this->result->merge($this->handleSideEffect(
                UpdatePermitFee::create(
                    [
                        'ecmtPermitApplicationId' => $ecmtApplication->getId(),
                        'licenceId' => $licence->getId(),
                        'permitsRequired' => $command->getPermitsRequired(),
                        'permitType' => $ecmtApplication::PERMIT_TYPE,
                        'receivedDate' => $ecmtApplication->getDateReceived()
                    ]
                )
            ));
        }

        $ecmtApplication->updatePermitsRequired($command->getPermitsRequired());

        $this->getRepo()->save($ecmtApplication);

        $this->result->addId('ecmtPermitsRequired', $ecmtApplication->getId());
        $this->result->addMessage('ECMT Permit Application Permits Required updated');

        return $this->result;
    }
}
