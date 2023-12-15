<?php

/**
 * Reset Variation
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Variation\ResetVariation as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\ApplicationResetTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation as CreateVariationCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Reset Variation
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class ResetVariation extends AbstractCommandHandler implements TransactionedInterface
{
    use ApplicationResetTrait;

    protected $repoServiceName = 'Application';

    /**
     * @param Cmd|CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $this->validate($command);

        $count = $this->closeTasks($application);
        $this->result->addMessage($count . ' task(s) closed');

        $licenceId = $application->getLicence()->getId();
        $receivedDate = $application->getReceivedDate();
        $appliedVia = $application->getAppliedVia();

        $this->getRepo()->delete($application);
        $this->result->addMessage('Variation removed');

        $this->result->merge(
            $this->createNewVariation($licenceId, $receivedDate, $appliedVia)
        );

        return $this->result;
    }

    /**
     * Create the new variation from the provided parameters
     *
     * @param int $licenceId
     * @param DateTime|null $receivedDate
     * @param RefData $appliedVia
     *
     * @return Result
     */
    private function createNewVariation($licenceId, $receivedDate, RefData $appliedVia): Result
    {
        $data = [
            'id' => $licenceId,
            'appliedVia' => $appliedVia->getId(),
            'receivedDate' => $this->processReceivedDate($receivedDate),
        ];

        return $this->handleSideEffect(
            CreateVariationCommand::create($data)
        );
    }

    /**
     * If the user is required to confirm this change, throw an exception to the front end to indicate as such
     *
     * @param Cmd $command
     *
     * @throws RequiresConfirmationException
     */
    private function validate(Cmd $command): void
    {
        if ($command->getConfirm() === false) {
            throw new RequiresConfirmationException(
                'Updating these elements requires confirmation',
                Application::ERROR_REQUIRES_CONFIRMATION
            );
        }
    }
}
