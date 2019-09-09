<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Permits\ActiveEcmtApplication as ActiveApplicationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Retrieve active ECMT application by licence and year
 */
final class ActiveEcmtApplication extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['IrhpPermitWindow'];
    protected $bundle = ['licence'];

    /**
     * @param QueryInterface|ActiveApplicationQuery $query query
     * @return Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var EcmtPermitApplication $ecmtApplicationRepo */
        $ecmtApplicationRepo = $this->getRepo();
        $applicationsByLicence = $ecmtApplicationRepo->fetchByLicence($query->getLicence());

        /** @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $window */
        $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByIrhpPermitType(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
            new DateTime(),
            Query::HYDRATE_OBJECT,
            $query->getYear()
        );

        /** @var \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication $application */
        foreach ($applicationsByLicence as $application) {
            $applicationStockId = $application->getFirstIrhpPermitApplication()->getIrhpPermitWindow()->getIrhpPermitStock()->getId();
            if (($applicationStockId === $window->getIrhpPermitStock()->getId()) && $application->isActive()) {
                return $this->result(
                    $application,
                    $this->bundle
                );
            }
        }

        return null;
    }
}
