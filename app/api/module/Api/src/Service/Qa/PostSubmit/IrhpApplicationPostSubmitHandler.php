<?php

namespace Dvsa\Olcs\Api\Service\Qa\PostSubmit;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class IrhpApplicationPostSubmitHandler
{
    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return IrhpApplicationPostSubmitHandler
     */
    public function __construct(IrhpPermitRepository $irhpPermitRepo)
    {
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Perform any required steps following the saving of the application
     *
     * @param IrhpApplication $irhpApplication
     */
    public function handle(IrhpApplication $irhpApplication)
    {
        if (!$irhpApplication->getIrhpPermitType()->isEcmtRemoval()) {
            return;
        }

        $irhpPermits = $irhpApplication->getFirstIrhpPermitApplication()->getIrhpPermits();
        foreach ($irhpPermits as $irhpPermit) {
            $irhpPermit->regenerateIssueDateAndExpiryDate();
            $this->irhpPermitRepo->save($irhpPermit);
        }
    }
}
