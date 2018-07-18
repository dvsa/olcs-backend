<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;

/**
 * Retrieve a permit application by id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class ById extends AbstractQueryByIdHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $bundle = ['licence'];
}
