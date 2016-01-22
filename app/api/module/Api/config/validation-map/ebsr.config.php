<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsLocalAuthorityUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsOperatorUser;

return [
    QueryHandler\Bus\Ebsr\TxcInboxList::class                                       => IsLocalAuthorityUser::class,
    QueryHandler\Bus\Ebsr\EbsrSubmissionList::class                                 => IsOperatorUser::class,
];
