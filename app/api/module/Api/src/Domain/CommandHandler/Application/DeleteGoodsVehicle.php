<?php

/**
 * Delete Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Vehicle\DeleteGoodsVehicle as VehicleCmd;

/**
 * Delete Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->proxyCommand($command, VehicleCmd::class));

        $dtoData = ['id' => $command->getApplication(), 'section' => 'vehicles'];
        $result->merge($this->handleSideEffect(UpdateApplicationCompletion::create($dtoData)));

        return $result;
    }
}
