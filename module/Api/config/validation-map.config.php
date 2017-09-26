<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NotIsAnonymousUser;

// @codingStandardsIgnoreStart
$map = [
    QueryHandler\CompaniesHouse\GetList::class => NotIsAnonymousUser::class,
];
// @codingStandardsIgnoreEnd

// Merge all other validation maps
foreach (glob(__DIR__ . '/validation-map/*.config.php') as $filename) {
    $map += include($filename);
}

return $map;
