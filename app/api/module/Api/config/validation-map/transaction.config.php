<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessApplicationWithId;

return [
    CommandHandler\Transaction\ReverseTransaction::class => IsInternalUser::class,
    CommandHandler\Transaction\AdjustTransaction::class => IsInternalUser::class,
];
