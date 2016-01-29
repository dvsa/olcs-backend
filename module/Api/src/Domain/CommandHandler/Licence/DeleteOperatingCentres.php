<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteApplicationLinks;
use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteConditionUndertakings;
use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteTmLinks;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as Cmd;

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceOperatingCentre'];

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $locs = $licence->getOperatingCentres();

        $count = 0;

        /** @var LicenceOperatingCentre $loc */
        foreach ($locs as $loc) {
            if (in_array($loc->getId(), $command->getIds())) {
                $message = $loc->checkCanDelete();
                if ($message) {
                    throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException(key($message));
                }

                $count++;
                $this->getRepo('LicenceOperatingCentre')->delete($loc);
                $this->result->merge($this->deleteConditionUndertakings($loc));
                $this->result->merge($this->deleteTransportManagerLinks($loc));
                $this->result->merge($this->deleteFromOtherApplications($loc));
            }
        }

        $this->result->addMessage($count . ' Operating Centre(s) removed');

        return $this->result;
    }

    /**
     * @param LicenceOperatingCentre $loc
     * @return Result
     */
    private function deleteConditionUndertakings($loc)
    {
        return $this->handleSideEffect(
            DeleteConditionUndertakings::create(
                [
                    'operatingCentre' => $loc->getOperatingCentre(),
                    'licence' => $loc->getLicence(),
                ]
            )
        );
    }

    private function deleteTransportManagerLinks($loc)
    {
        return $this->handleSideEffect(
            DeleteTmLinks::create(['operatingCentre' => $loc->getOperatingCentre()])
        );
    }

    private function deleteFromOtherApplications($loc)
    {
        return $this->handleSideEffect(
            DeleteApplicationLinks::create(['operatingCentre' => $loc->getOperatingCentre()])
        );
    }
}
