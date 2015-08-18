<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    // Queries
    QueryHandler\MyAccount\MyAccount::class                         => NoValidationRequired::class,
    QueryHandler\User\Partner::class                                => IsInternalUser::class,
    QueryHandler\User\PartnerList::class                            => IsInternalUser::class,
    QueryHandler\User\RoleList::class                               => NoValidationRequired::class,

    // Commands
    CommandHandler\User\CreatePartner::class                        => IsInternalUser::class,
    CommandHandler\User\DeletePartner::class                        => IsInternalUser::class,
    CommandHandler\User\RegisterUserSelfserve::class                => NoValidationRequired::class,
    CommandHandler\User\RemindUsernameSelfserve::class              => NoValidationRequired::class,
    CommandHandler\User\UpdatePartner::class                        => IsInternalUser::class,
];
