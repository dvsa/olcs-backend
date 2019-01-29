<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ActiveApplication as ActiveApplicationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;

/**
 * Retrieve active IRHP application by licence and permit type
 */
final class ActiveApplication extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpApplication';
    protected $bundle = ['licence', 'irhpPermitType'];

    /**
     * @param QueryInterface|ActiveApplicationQuery $query query
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrhpApplication $irhpApplicationRepo */
        $irhpApplicationRepo = $this->getRepo();
        $applicationsByLicence = $irhpApplicationRepo->fetchByLicence((int) $query->getLicence());

        /** @var \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication $application */
        foreach ($applicationsByLicence as $application) {
            if (($application->getIrhpPermitType()->getId() == $query->getIrhpPermitType()) && $application->isActive()) {
                return $this->result(
                    $application,
                    $this->bundle
                );
            }
        }

        return null;
    }
}
