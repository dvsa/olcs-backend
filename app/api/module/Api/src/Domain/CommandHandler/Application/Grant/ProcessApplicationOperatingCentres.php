<?php

/**
 * Process Application Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\EntityCloner;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as Aoc;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as Loc;

/**
 * Process Application Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ProcessApplicationOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['LicenceOperatingCentre', 'ApplicationOperatingCentre'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);
        $licence = $application->getLicence();

        $applicationOperatingCentres = $application->getOperatingCentres();

        $add = $update = $delete = 0;

        /** @var Aoc $aoc */
        foreach ($applicationOperatingCentres as $aoc) {

            if ($aoc->getIsInterim()) {
                $aoc->setIsInterim(false);
                $this->getRepo('ApplicationOperatingCentre')->save($aoc);
            }

            switch ($aoc->getAction()) {
                case 'A':
                    $this->addLicenceOperatingCentre($aoc, $licence);
                    $add++;
                    break;
                case 'U':
                    $loc = $this->findCorrespondingLoc($aoc, $licence);
                    $this->updateLicenceOperatingCentre($aoc, $loc);
                    $update++;
                    break;
                case 'D':
                    $this->deleteLicenceOperatingCentre($aoc, $licence);
                    $delete++;
                    break;
            }
        }

        $result->addMessage($add . ' licence operating centre(s) created');
        $result->addMessage($update . ' licence operating centre(s) updated');
        $result->addMessage($delete . ' licence operating centre(s) removed');

        return $result;
    }

    protected function findCorrespondingLoc(Aoc $aoc, Licence $licence)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->eq(
                'operatingCentre',
                $aoc->getOperatingCentre()
            )
        );

        $locs = $licence->getOperatingCentres()->matching($criteria);

        if ($locs->count() !== 1) {
            throw new \Exception('Expected 1 matching licence operating centre record, found: ' . $locs->count());
        }

        return $locs->first();
    }

    protected function addLicenceOperatingCentre(Aoc $aoc, Licence $licence)
    {
        $loc = new Loc($licence, $aoc->getOperatingCentre());

        $this->updateLicenceOperatingCentre($aoc, $loc);
    }

    protected function deleteLicenceOperatingCentre(Aoc $aoc, Licence $licence)
    {
        $loc = $this->findCorrespondingLoc($aoc, $licence);
        $this->getRepo('LicenceOperatingCentre')->delete($loc);
    }

    protected function updateLicenceOperatingCentre(Aoc $aoc, Loc $loc)
    {
        $ignore = [
            'id',
            'action',
            'version',
            'createdOn',
            'createdBy',
            'modifiedOn',
            'modifiedBy',
            'isInterim'
        ];

        EntityCloner::cloneEntityInto($aoc, $loc, $ignore);

        $this->getRepo('LicenceOperatingCentre')->save($loc);
    }
}
