<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
  QueryHandler\Permits\SectorsList::class => NoValidationRequired::class,
  QueryHandler\Permits\ConstrainedCountries::class => NoValidationRequired::class,
  QueryHandler\Permits\EcmtPermits::class => NoValidationRequired::class,
  QueryHandler\Permits\EcmtPermitApplication::class => NoValidationRequired::class,
  QueryHandler\Permits\ById::class => NoValidationRequired::class,
  CommandHandler\Permits\CreateEcmtPermits::class => NoValidationRequired::class,
  CommandHandler\Permits\CreateEcmtPermitApplication::class => NoValidationRequired::class,
  CommandHandler\Permits\UpdateEcmtEmissions::class => NoValidationRequired::class,
  CommandHandler\Permits\CancelEcmtPermitApplication::class => NoValidationRequired::class,
  CommandHandler\Permits\UpdateDeclaration::class => NoValidationRequired::class,
  CommandHandler\Permits\UpdateEcmtPermitsRequired::class => NoValidationRequired::class,
];
