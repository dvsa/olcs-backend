<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Submission as SubmissionQueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission as AppCommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers as Handler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Submission as SubmissionHandler;

return [
    // Queries
    SubmissionQueryHandler\Submission::class => Handler\Standard::class, // @todo
    SubmissionQueryHandler\SubmissionAction::class => Handler\Standard::class, // @todo
    SubmissionQueryHandler\SubmissionList::class => Handler\Standard::class, // @todo
    SubmissionQueryHandler\SubmissionSectionComment::class => Handler\Standard::class, // @todo

    // Commands

    // Submission
    AppCommandHandler\CloseSubmission::class => SubmissionHandler\Close::class,
    AppCommandHandler\ReopenSubmission::class => SubmissionHandler\Reopen::class,
    AppCommandHandler\UpdateSubmission::class => Handler\Standard::class, // @todo
    AppCommandHandler\DeleteSubmission::class => Handler\Standard::class, // @todo
    AppCommandHandler\AssignSubmission::class => Handler\Standard::class, // @todo
    AppCommandHandler\CreateSubmission::class => Handler\Standard::class, // @todo

    // Submission Action
    AppCommandHandler\CreateSubmissionAction::class => Handler\Standard::class, // @todo
    AppCommandHandler\UpdateSubmissionAction::class => Handler\Standard::class, // @todo

    // SubmissionSectionComment
    AppCommandHandler\CreateSubmissionSectionComment::class => Handler\Standard::class, // @todo
    AppCommandHandler\DeleteSubmissionSectionComment::class => Handler\Standard::class, // @todo
    AppCommandHandler\UpdateSubmissionSectionComment::class => Handler\Standard::class, // @todo

    // Others
    AppCommandHandler\FilterSubmissionSections::class => Handler\Standard::class, // @todo
    AppCommandHandler\RefreshSubmissionSections::class => Handler\Standard::class, // @todo

];
