<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
  CommandHandler\SystemParameter\Create::class            => IsInternalUser::class,
  CommandHandler\SystemParameter\Update::class            => IsInternalUser::class,
  CommandHandler\SystemParameter\Delete::class            => IsInternalUser::class,
  QueryHandler\SystemParameter\SystemParameter::class     => IsInternalUser::class,
  QueryHandler\SystemParameter\SystemParameterList::class => IsInternalUser::class,
];
