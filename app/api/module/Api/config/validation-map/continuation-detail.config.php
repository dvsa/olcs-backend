<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\ContinuationDetail\CanAccessContinuationDetailWithId;

return [
    QueryHandler\ContinuationDetail\LicenceChecklist::class => CanAccessContinuationDetailWithId::class,
    QueryHandler\ContinuationDetail\Review::class => CanAccessContinuationDetailWithId::class,
];
