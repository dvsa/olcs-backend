<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTaxiPhv as Command;

/**
 * Update TaxiPhv
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateTaxiPhv extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @param Command $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $application = $this->getRepo()->fetchUsingId($command);

        $result = new Result();
        $result->merge($this->updatePrivateHireLicence($command));
        $result->merge($this->updateApplicationCompletion($application));

        return $result;
    }

    /**
     * Create PrivateHireLicence entity
     *
     * @param Application $application
     * @param Command $command
     *
     * @return Result
     */
    private function updatePrivateHireLicence(Command $command)
    {
        $data = [
            'id' => $command->getPrivateHireLicence(),
            'version' => $command->getVersion(),
            'privateHireLicenceNo' => $command->getPrivateHireLicenceNo(),
            'councilName' => $command->getCouncilName(),
            'address' => $command->getAddress(),
        ];

        return $this->handleSideEffect(\Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update::create($data));
    }

    /**
     * Update the ApplicationCompletion
     *
     * @param Application $application
     *
     * @return Result
     */
    private function updateApplicationCompletion(Application $application)
    {
        $data = [
            'id' => $application->getId(),
            'section' => 'taxiPhv',
        ];

        return $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::create($data)
        );
    }
}
