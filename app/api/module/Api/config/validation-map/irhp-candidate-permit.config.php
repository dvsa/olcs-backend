<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits\CanAccessIrhpApplicationWithIrhpApplication;

return [
    QueryHandler\IrhpCandidatePermit\GetList::class => IsInternalUser::class,
    QueryHandler\IrhpCandidatePermit\GetListByIrhpApplication::class
        => CanAccessIrhpApplicationWithIrhpApplication::class,
];
