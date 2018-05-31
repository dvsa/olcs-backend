<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
  QueryHandler\Permits\SectorsList::class => NoValidationRequired::class,
];
