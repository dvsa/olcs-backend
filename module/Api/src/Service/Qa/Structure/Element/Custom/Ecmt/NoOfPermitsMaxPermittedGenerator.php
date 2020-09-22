<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use RuntimeException;

class NoOfPermitsMaxPermittedGenerator
{
    const ECMT_SHORT_TERM_MULTIPLIER = 2;
    const ERR_INVALID_TYPE = 'NoOfPermitsMaxPermittedGenerator is only applicable to short terms and annuals';

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return NoOfPermitsMaxPermittedGenerator
     */
    public function __construct(IrhpPermitRepository $irhpPermitRepo)
    {
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Generate a value for the maximum number of permits value shown on the number of permits page
     *
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public function generate(IrhpApplicationEntity $irhpApplicationEntity)
    {
        $irhpPermitType = $irhpApplicationEntity->getIrhpPermitType();

        if (!$irhpPermitType->isEcmtShortTerm() && !$irhpPermitType->isEcmtAnnual()) {
            throw new RuntimeException(self::ERR_INVALID_TYPE);
        }

        $totAuthVehicles = $irhpApplicationEntity->getLicence()->getTotAuthVehicles();

        if ($irhpPermitType->isEcmtShortTerm()) {
            return $totAuthVehicles * self::ECMT_SHORT_TERM_MULTIPLIER;
        }

        $licenceId = $irhpApplicationEntity->getLicence()->getId();
        $stockEndYear = $irhpApplicationEntity->getAssociatedStock()->getValidityYear();

        $allocatedPermitCount = $this->irhpPermitRepo->getEcmtAnnualPermitCountByLicenceAndStockEndYear(
            $licenceId,
            $stockEndYear
        );

        return $totAuthVehicles - $allocatedPermitCount;
    }
}
