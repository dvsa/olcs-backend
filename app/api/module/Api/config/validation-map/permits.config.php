<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
  QueryHandler\Permits\SectorsList::class => IsInternalUser::class,
];
