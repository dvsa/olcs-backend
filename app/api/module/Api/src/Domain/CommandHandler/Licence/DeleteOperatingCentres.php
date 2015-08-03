<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

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
                $count++;
                $this->getRepo('LicenceOperatingCentre')->delete($loc);
            }
        }

        $this->result->addMessage($count . ' Operating Centre(s) removed');

        return $this->result;
    }
}
