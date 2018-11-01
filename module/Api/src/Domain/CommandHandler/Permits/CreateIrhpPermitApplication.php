<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Irhp Permit Application
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class CreateIrhpPermitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermitApplication = $this->getRepo()->getReference(
            EcmtPermitApplicationEntity::class,
            $command->getEcmtPermitApplication()
        );

        $entity = IrhpPermitApplicationEntity::createNew(
            $this->getRepo()->getReference(IrhpPermitWindowEntity::class, $command->getWindow()),
            $ecmtPermitApplication->getLicence(),
            $ecmtPermitApplication
        );

        $this->getRepo()->save($entity);

        $this->result->addId('irhpPermitApplication', $entity->getId());
        $this->result->addMessage('IRHP Permit Application created successfully');

        return $this->result;
    }
}
