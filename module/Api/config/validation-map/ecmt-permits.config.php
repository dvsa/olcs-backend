<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
  QueryHandler\EcmtPermits\EcmtPermits::class => IsInternalUser::class,
  QueryHandler\EcmtPermits\SiftingSettings::class => IsInternalUser::class,
  CommandHandler\EcmtPermits\GenerateEcmtPermit::class => IsInternalUser::class,
];
