<?php

/**
 * Does Submission belong to case
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Does Submission belong to case
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SubmissionBelongsToCase extends AbstractBelongsToCaseEntity
{
    protected $repo = 'Submission';
}
