<?php


namespace Dvsa\Olcs\Api\Service\Submission\Process;

use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Service\Submission\ImmutableArrayObject;

/**
 * Interface ProcessInterface
 * @package Dvsa\Olcs\Api\Service\Submission\Process
 */
interface ProcessInterface
{
    public function process(SubmissionEntity $submission, ImmutableArrayObject $context);
}
