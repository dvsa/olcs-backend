<?php

/**
 * Can Submission be closed
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can Submission be closed
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanCloseSubmission extends AbstractCanCloseEntity
{
    protected $repo = 'Submission';
}
