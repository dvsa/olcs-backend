<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a list of Constrained Countries
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class ConstrainedCountries extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'Country';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();
        $constrainedCountries = $repo->getConstrainedEcmtCountries();
        return [
          'result' => $constrainedCountries[1],
          'count' => $constrainedCountries[0],
        ];
    }
}
