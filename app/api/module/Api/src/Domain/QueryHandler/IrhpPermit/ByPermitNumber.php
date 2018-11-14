<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Query\IrhpPermit\ByPermitNumber as ByPermitNumberQry;

/**
 * IrhpPermit by Permit Number
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ByPermitNumber extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * @param QueryInterface|ByPermitNumberQry $query query
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchByNumberAndRange($query->getPermitNumber(), $query->getIrhpPermitRange());
    }
}
