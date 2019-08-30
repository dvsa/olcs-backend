<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ActiveApplication as ActiveApplicationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Retrieve active IRHP application by licence and permit type
 */
final class ActiveApplication extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';
    protected $bundle = ['licence', 'irhpPermitType'];

    /**
     * @param QueryInterface|ActiveApplicationQuery $query query
     *
     * @return Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrhpApplication $irhpApplicationRepo */
        $irhpApplicationRepo = $this->getRepo();
        $applicationsByLicence = $irhpApplicationRepo->fetchByLicence((int) $query->getLicence());

        /** @var \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication $application */
        foreach ($applicationsByLicence as $application) {
            if ($application->getIrhpPermitType()->getId() == $query->getIrhpPermitType() &&
                $application->isActive() &&
                $this->applicationMatchesYearCriteria($application, $query->getYear())
            ) {
                return $this->result(
                    $application,
                    $this->bundle
                );
            }
        }

        return null;
    }

    /**
     * Whether the validity year of the provided application matches that provided on the query
     *
     * @param IrhpApplication $application
     * @param int|null $queryValidityYear
     *
     * @return bool
     */
    private function applicationMatchesYearCriteria(IrhpApplicationEntity $application, $queryValidityYear)
    {
        if (!$queryValidityYear) {
            return true;
        }

        $applicationValidityYear = $application->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getValidityYear();

        return $applicationValidityYear == $queryValidityYear;
    }
}
