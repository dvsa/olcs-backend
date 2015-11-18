<?php

/**
 * Update Variation Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\VariationOperatingCentre;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\VariationOperatingCentre\Update as Cmd;
use Dvsa\Olcs\Transfer\Command\ApplicationOperatingCentre\Update as AppUpdate;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as LicenceOperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Update Variation Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOperatingCentre', 'LicenceOperatingCentre'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $id = $command->getId();

        list($prefix, $id) = $this->splitTypeAndId($id);

        // If it's a delta, we can just edit it anytime
        if ($prefix === 'A') {

            $aoc = $this->getRepo('ApplicationOperatingCentre')->fetchById($id);

            if (in_array($aoc->getAction(), ['A', 'U'])) {

                $data = $command->getArrayCopy();
                $data['id'] = $id;

                if ($aoc->getAction() === 'U') {
                    unset($data['address']);
                }

                return $this->handleSideEffect(AppUpdate::create($data));
            }

            throw new ForbiddenException('You are not allowed to update this record');
        }

        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        /** @var LicenceOperatingCentreEntity $loc */
        $loc = $this->getRepo('LicenceOperatingCentre')->fetchById($id);
        $oc = $loc->getOperatingCentre();

        $deltaRecords = $application->getDeltaAocByOc($oc);

        if ($deltaRecords->isEmpty()) {
            $aoc = new ApplicationOperatingCentre($application, $oc);
            $aoc->setAction('U');
            $aoc->setAdPlaced(false);

            $this->getRepo('ApplicationOperatingCentre')->save($aoc);

            $data = $command->getArrayCopy();
            $data['id'] = $aoc->getId();
            $data['version'] = $aoc->getVersion();
            unset($data['address']);
            return $this->handleSideEffect(AppUpdate::create($data));
        }

        throw new ForbiddenException('You are not allowed to update this record');
    }

    private function splitTypeAndId($ref)
    {
        $type = substr($ref, 0, 1);

        $id = (int)substr($ref, 1);

        return [$type, $id];
    }
}
