<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;

/**
 * EcmtPermitApplication
 *
 * @author Andy Newton
 */
class GetEcmtPermitApplicationList extends AbstractListQueryHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';
}
