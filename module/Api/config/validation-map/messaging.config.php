<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    QueryHandler\Messaging\Conversations\ByLicence::class              => NoValidationRequired::class,
    QueryHandler\Messaging\Conversations\ByApplicationToLicence::class => NoValidationRequired::class,
    QueryHandler\Messaging\ApplicationLicenceList\ByOrganisation::class => NoValidationRequired::class,
    QueryHandler\Messaging\ApplicationLicenceList\ByApplicationToOrganisation::class => NoValidationRequired::class,
    QueryHandler\Messaging\ApplicationLicenceList\ByLicenceToOrganisation::class     => NoValidationRequired::class,
    QueryHandler\Messaging\Message\ByConversation::class                             => NoValidationRequired::class,
    CommandHandler\Messaging\Conversation\Close::class                               => NoValidationRequired::class,
    CommandHandler\Messaging\Conversation\Disable::class                             => NoValidationRequired::class,
    CommandHandler\Messaging\Conversation\Enable::class                              => NoValidationRequired::class,
    CommandHandler\Messaging\Message\Create::class                                   => NoValidationRequired::class,
    QueryHandler\Messaging\Conversations\ByOrganisation::class                       => NoValidationRequired::class,
    QueryHandler\Messaging\Subjects\All::class                                       => NoValidationRequired::class,
    CommandHandler\Messaging\Conversation\Create::class                              => NoValidationRequired::class,
    CommandHandler\Email\SendNewMessageNotificationToOperators::class                => IsSideEffect::class,
];
