<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;

return [
    QueryHandler\IrhpCandidatePermit\GetScoredList::class =>  NotIsAnonymousUser::class,
];
