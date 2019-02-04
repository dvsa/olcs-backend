<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessLicenceWithLicence;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits\CanEditIrhpApplicationWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    CommandHandler\IrhpApplication\Create::class => CanAccessLicenceWithLicence::class,
    CommandHandler\IrhpApplication\UpdateLicence::class => CanEditIrhpApplicationWithId::class,
    CommandHandler\IrhpApplication\CreateFull::class => IsInternalUser::class,
    CommandHandler\IrhpApplication\UpdateFull::class => CanEditIrhpApplicationWithId::class,
];
