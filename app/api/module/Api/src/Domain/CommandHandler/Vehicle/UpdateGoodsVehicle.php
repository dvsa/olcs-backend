<?php

/**
 * Update Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Update Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'LicenceVehicle';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        if ($command->getRemovalDate() !== null && !$this->isGranted(Permission::INTERNAL_USER)) {
            throw new ForbiddenException();
        }

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $licenceVehicle->getVehicle()->setPlatedWeight($command->getPlatedWeight());
        if ($command->getSpecifiedDate() !== null) {
            $licenceVehicle->setSpecifiedDate(new \DateTime($command->getSpecifiedDate()));
        }
        if ($command->getReceivedDate() !== null) {
            $licenceVehicle->setReceivedDate(new \DateTime($command->getReceivedDate()));
        }
        if ($command->getRemovalDate() !== null) {
            $licenceVehicle->setRemovalDate(new \DateTime($command->getRemovalDate()));
        }

        $this->getRepo()->save($licenceVehicle);

        $result->addMessage('Vehicle updated');

        return $result;
    }
}
