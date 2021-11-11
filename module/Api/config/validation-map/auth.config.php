<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    CommandHandler\Auth\Login::class => NoValidationRequired::class,
    CommandHandler\Auth\LoginFactory::class => NoValidationRequired::class,
    CommandHandler\Auth\ChangePassword::class => NotIsAnonymousUser::class,
    CommandHandler\Auth\ChangePasswordFactory::class => NotIsAnonymousUser::class,
    CommandHandler\Auth\RefreshToken::class => NotIsAnonymousUser::class,
    CommandHandler\Auth\RefreshTokenFactory::class => NotIsAnonymousUser::class,
];
