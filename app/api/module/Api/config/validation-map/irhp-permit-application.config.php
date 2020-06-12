<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsSideEffect;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits\CanEditIrhpPermitApplicationWithId;

return [
    CommandHandler\IrhpPermitApplication\Delete::class => CanEditIrhpPermitApplicationWithId::class,
    CommandHandler\IrhpPermitApplication\CreateForIrhpApplication::class => IsSideEffect::class,
    CommandHandler\IrhpPermitApplication\UpdateIrhpPermitWindow::class => IsSideEffect::class,
];
