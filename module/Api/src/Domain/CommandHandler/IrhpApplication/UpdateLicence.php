<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\ResetIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update IRHP Application Licence
 */
final class UpdateLicence extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    const LICENCE_INVALID_MSG = 'Licence ID %s with number %s is unable to make an IRHP application';
    const LICENCE_ORG_MSG = 'Licence does not belong to this organisation';

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['Licence'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());

        if (!$this->licenceBelongsToOrganisation($licence)) {
            throw new ForbiddenException(self::LICENCE_ORG_MSG);
        }

        /** @var IrhpApplication $application */
        $application = $this->getRepo()->fetchById($command->getId());

        if (!$application->isMultiStock()
            && !$licence->canMakeIrhpApplication($application->getAssociatedStock(), $application)
        ) {
            $message = sprintf(self::LICENCE_INVALID_MSG, $licence->getId(), $licence->getLicNo());
            throw new ForbiddenException($message);
        }

        $this->result->merge(
            $this->handleSideEffect(
                ResetIrhpPermitApplications::create(['id' => $command->getId()])
            )
        );

        // Update the licence but reset the previously answers questions to NULL
        $application->updateLicence($licence);
        $fees = $application->getOutstandingFees();

        /** @var Fee $fee */
        foreach ($fees as $fee) {
            $this->result->merge($this->handleSideEffect(CancelFee::create(['id' => $fee->getId()])));
        }

        $this->getRepo()->save($application);
        $this->result->addId('irhpApplication', $application->getId());
        $this->result->addMessage('IrhpApplication Licence Updated successfully');

        return $this->result;
    }
}
