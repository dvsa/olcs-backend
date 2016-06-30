<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Standard;

/**
 * @NOTE When you implement one of the following rules, please move it to the (or create a) relevant
 * validation-map/*.config.php. Eventually this file should be empty
 */
// @codingStandardsIgnoreStart
$map = [
    CommandHandler\Bus\Ebsr\UpdateTxcInbox::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Application\Create::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Application\CreateOfficeCopy::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Licence\Create::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Licence\CreateOfficeCopy::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Reprint::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Restore::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Stop::class => Standard::class, // @todo
    CommandHandler\CommunityLic\Void::class => Standard::class, // @todo
    CommandHandler\ConditionUndertaking\Create::class => Standard::class, // @todo
    CommandHandler\ConditionUndertaking\DeleteList::class => Standard::class, // @todo
    CommandHandler\ConditionUndertaking\Update::class => Standard::class, // @todo
    CommandHandler\Email\SendTmApplication::class => Standard::class, // @todo
    CommandHandler\Organisation\UpdateBusinessType::class => Standard::class, // @todo
    CommandHandler\OtherLicence\CreateForTma::class => Standard::class, // @todo
    CommandHandler\OtherLicence\CreatePreviousLicence::class => Standard::class, // @todo
    CommandHandler\OtherLicence\UpdateForTma::class => Standard::class, // @todo
    CommandHandler\PrivateHireLicence\Create::class => Standard::class, // @todo
    CommandHandler\PrivateHireLicence\DeleteList::class => Standard::class, // @todo
    CommandHandler\PrivateHireLicence\Update::class => Standard::class, // @todo
    CommandHandler\TmEmployment\Create::class => Standard::class, // @todo
    CommandHandler\TmEmployment\DeleteList::class => Standard::class, // @todo
    CommandHandler\TmEmployment\Update::class => Standard::class, // @todo
    CommandHandler\Variation\DeleteListConditionUndertaking::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateAddresses::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateConditionUndertaking::class => Standard::class, // @todo
    CommandHandler\Variation\UpdateTypeOfLicence::class => Standard::class, // @todo
    QueryHandler\ApplicationOperatingCentre\ApplicationOperatingCentre::class => Standard::class, // @todo
    QueryHandler\Bus\Ebsr\BusRegWithTxcInbox::class => Standard::class, // @todo
    QueryHandler\Bus\RegistrationHistoryList::class => Standard::class, // @todo
    QueryHandler\Bus\SearchViewList::class => Standard::class, // @todo
    QueryHandler\CompaniesHouse\GetList::class => Standard::class, // @todo
    QueryHandler\ConditionUndertaking\Get::class => Standard::class, // @todo
    QueryHandler\ConditionUndertaking\GetList::class => Standard::class, // @todo
    QueryHandler\Organisation\Dashboard::class => Standard::class, // @todo
    QueryHandler\Organisation\Organisation::class => Standard::class, // @todo
    QueryHandler\Organisation\OutstandingFees::class => Standard::class, // @todo
    QueryHandler\OtherLicence\GetList::class => Standard::class, // @todo
    QueryHandler\Search\Licence::class => Standard::class, // @todo
    QueryHandler\TmEmployment\GetSingle::class => Standard::class, // @todo
    QueryHandler\Variation\TypeOfLicence::class => Standard::class, // @todo
];
// @codingStandardsIgnoreEnd

// Merge all other validation maps
foreach (glob(__DIR__ . '/validation-map/*.config.php') as $filename) {
    $map += include($filename);
}

return $map;
