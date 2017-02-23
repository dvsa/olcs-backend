<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsOperatorUser;

return [
    QueryHandler\GdsVerify\GetAuthRequest::class        => IsOperatorUser::class,
    CommandHandler\GdsVerify\ProcessSignatureResponse::class     => IsOperatorUser::class,
];
