<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepository;
use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;

class AnswerWriter
{
    /** @var IrhpPermitApplicationFactory */
    private $irhpPermitApplicationFactory;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitWindowRepo;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationFactory $irhpPermitApplicationFactory
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitWindowRepository $irhpPermitWindowRepo
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     *
     * @return AnswerSaver
     */
    public function __construct(
        IrhpPermitApplicationFactory $irhpPermitApplicationFactory,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitWindowRepository $irhpPermitWindowRepo,
        CurrentDateTimeFactory $currentDateTimeFactory
    ) {
        $this->irhpPermitApplicationFactory = $irhpPermitApplicationFactory;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitWindowRepo = $irhpPermitWindowRepo;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
    }

    /**
     * Write the number of permits required to persistent storage
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitsRequired
     */
    public function write(IrhpApplicationEntity $irhpApplication, $permitsRequired)
    {
        $irhpPermitApplication = $irhpApplication->getIrhpPermitApplications()->first();
        if (!is_object($irhpPermitApplication)) {
            $lastOpenWindow = $this->irhpPermitWindowRepo->fetchLastOpenWindowByIrhpPermitType(
                IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                $this->currentDateTimeFactory->create()
            );

            $irhpPermitApplication = $this->irhpPermitApplicationFactory->create($irhpApplication, $lastOpenWindow);
        }

        $irhpPermitApplication->updatePermitsRequired($permitsRequired);
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
