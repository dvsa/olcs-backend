<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\LastOpenWindow as LastOpenWindowQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use DateTime;

/**
 * Last open window
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class LastOpenWindow extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermitWindow';

    /**
     * Handle query
     *
     * @param QueryInterface|LastOpenWindowQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $lastOpenWindow = $this->getRepo()->fetchLastOpenWindow(
            DateTime::createFromFormat('Y-m-d H:i:s', $query->getCurrentDateTime())
        );

        return ['result' => $lastOpenWindow];
    }
}
