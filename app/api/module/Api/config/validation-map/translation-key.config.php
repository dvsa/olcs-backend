<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSystemAdmin;

return [
    QueryHandler\TranslationKey\ById::class => IsSystemAdmin::class,
    QueryHandler\TranslationKey\GetList::class => IsSystemAdmin::class,
];
