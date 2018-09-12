<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Query\QueryInterface;


/*
* Calculates the MeanDeviation for use in other calculations
*/
class MeanDeviation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['EcmtPermitApplication'];

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        $licences = $repo->getLicences();
        $licenceCount = count($licences);

        //Count the permitsRequired for each licence
        $permitsRequestedCount = 0;
        foreach ($licences as $licence) {
            $ecmtPermitApp = $this->getRepo('EcmtPermitApplication')->fetchByLicenceId($licence->getId());
            $permitsRequestedCount += $ecmtPermitApp->getPermitsRequired();
        }

        return $permitsRequestedCount / $licenceCount;
    }
}
