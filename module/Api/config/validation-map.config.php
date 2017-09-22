<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Standard;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

/**
 * @NOTE When you implement one of the following rules, please move it to the (or create a) relevant
 * validation-map/*.config.php. Eventually this file should be empty
 */
// @codingStandardsIgnoreStart
$map = [
    CommandHandler\Variation\DeleteListConditionUndertaking::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateAddresses::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateConditionUndertaking::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateTypeOfLicence::class => Standard::class, // @todo
    QueryHandler\ApplicationOperatingCentre\ApplicationOperatingCentre::class => Standard::class, // @todo
    QueryHandler\CompaniesHouse\GetList::class => Standard::class, // @todo
    QueryHandler\Variation\TypeOfLicence::class => Standard::class, // @todo
];
// @codingStandardsIgnoreEnd

// Merge all other validation maps
foreach (glob(__DIR__ . '/validation-map/*.config.php') as $filename) {
    $map += include($filename);
}

return $map;
