<?php

/**
 * Handle Oc Variation Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Handle Oc Variation Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class HandleOcVariationFees extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $applicationOcs = $application->getOperatingCentres();

        $licenceOcs = $application->getLicence()->getOperatingCentres();

        if ($this->feeApplies($applicationOcs, $licenceOcs)) {
            $this->maybeCreateVariationFee($application);
        } else {
            $this->maybeCancelVariationFee($application);
        }

        return $this->result;
    }

// @todo check this
    private function maybeCreateVariationFee(Application $application)
    {
        $fees = $this->getRepo('Fee')->fetchOutstandingFeesByApplicationId($application->getId());

        if (empty($fees)) {
            $data = [
                'id' => $application->getId(),
                'feeTypeFeeType' => FeeType::FEE_TYPE_VAR
            ];

            $this->result->merge($this->handleSideEffect(CreateApplicationFeeCmd::create($data)));
        }
    }

    private function maybeCancelVariationFee(Application $application)
    {
        $fees = $this->getRepo('Fee')->fetchOutstandingFeesByApplicationId($application->getId());

        if (!empty($fees)) {
            /** @var Fee $fee */
            foreach ($fees as $fee) {
                $this->handleSideEffect(CancelFee::create(['id' => $fee->getId()]));
            }

            $this->result->addMessage(count($fees) . ' Fee(s) cancelled');
        }
    }

    private function feeApplies($applicationOcs, $licenceOcs)
    {
        foreach ($applicationOcs as $aoc) {

            switch ($aoc->getAction()) {
                case 'A':
                    // operating centre added, fee always applies
                    return true;
                case 'U':
                    // if there's an increase in auth. at a centre, fee applies
                    if ($this->hasIncreasedAuth($aoc, $licenceOcs)) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    }

    private function hasIncreasedAuth(ApplicationOperatingCentre $aoc, $licenceOcs)
    {
        /** @var LicenceOperatingCentre $loc */
        foreach ($licenceOcs as $loc) {
            if ($aoc->getOperatingCentre() === $loc->getOperatingCentre()) {
                if ($aoc->getNoOfVehiclesRequired() > $loc->getNoOfVehiclesRequired()) {
                    return true;
                }
                if ($aoc->getNoOfTrailersRequired() > $loc->getNoOfTrailersRequired()) {
                    return true;
                }
            }
        }

        return false;
    }
}
