<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;

return [
    CommandHandler\PartialMarkup\Update::class => IsSideEffect::class,
    CommandHandler\PartialMarkup\Create::class => IsSideEffect::class,
];
