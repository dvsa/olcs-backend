<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\LocalAuthority;

use Dvsa\Olcs\Api\Domain\QueryHandler\LocalAuthority\ById as ToggleByIdHandler;
use Dvsa\Olcs\Api\Domain\Repository\LocalAuthority as LocalAuthorityRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Transfer\Query\LocalAuthority\ById as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractQueryByIdHandlerTest;

/**
 * Local Authority ById Test
 */
class ByIdTest extends AbstractQueryByIdHandlerTest
{
    protected $sutClass = ToggleByIdHandler::class;
    protected $sutRepo = 'LocalAuthority';
    protected $qryClass = QryClass::class;
    protected $repoClass = LocalAuthorityRepo::class;
    protected $entityClass = LocalAuthorityEntity::class;
}
