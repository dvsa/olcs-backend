<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Submission as SubmissionQueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission as AppCommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Submission as SubmissionHandler;

return [
    // Submission
    AppCommandHandler\CloseSubmission::class => SubmissionHandler\Close::class,
    AppCommandHandler\ReopenSubmission::class => SubmissionHandler\Reopen::class,
];
