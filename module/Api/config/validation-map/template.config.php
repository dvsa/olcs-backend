<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalAdmin;

return [
    QueryHandler\Template\AvailableTemplates::class => IsInternalAdmin::class,
    QueryHandler\Template\PreviewTemplateSource::class => IsInternalAdmin::class,
    QueryHandler\Template\TemplateSource::class => IsInternalAdmin::class,
    CommandHandler\Template\UpdateTemplateSource::class => IsInternalAdmin::class,
];
