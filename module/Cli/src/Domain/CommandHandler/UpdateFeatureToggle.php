<?php


namespace Dvsa\Olcs\Cli\Domain\CommandHandler;


use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Cli\Domain\Command\UpdateFeatureToggle as UpdateToggleCmd;



/**
 * Class UpdateFeatureToggle
 *
 * @package Dvsa\Olcs\Cli\Domain\CommandHandler
 * 
 */
class UpdateFeatureToggle extends AbstractCommandHandler
{

    use AuthAwareTrait;

    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     *
     *
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $result = new Result();
        if ($this->isValidUser()) {
            $result = $this->proxyCommandAsSystemUser($command, UpdateToggleCmd::class
            );
        }

        return $result;
    }

    /**
     * validate the user running this is internal admin
     *
     * @return bool
     */
    public function isValidUser(): bool
    {
        return $this->isGranted(Permission::INTERNAL_ADMIN);
    }

}