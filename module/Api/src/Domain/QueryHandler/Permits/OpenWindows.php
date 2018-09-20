<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\OpenWindows as OpenWindowsQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use DateTime;

/**
 * Open windows
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OpenWindows extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermitWindow';

    /**
     * Handle query
     *
     * @param QueryInterface|OpenWindowsQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $openWindows = $this->getRepo()->fetchOpenWindows(
            DateTime::createFromFormat('Y-m-d H:i:s', $query->getCurrentDateTime())
        );

        return ['windows' => $openWindows];
    }
}
