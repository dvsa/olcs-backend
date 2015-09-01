<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;

/**
 * SubmissionGeneratorAwareTrait
 */
trait SubmissionGeneratorAwareTrait
{
    /**
     * @var SubmissionnGenerator
     */
    protected $submissionGenerator;

    /**
     * @param SubmissionnGenerator $service
     */
    public function setSubmissionGenerator(SubmissionGenerator $service)
    {
        $this->submissionGenerator = $service;
    }

    /**
     * @return SubmissionnGenerator
     */
    public function getSubmissionGenerator()
    {
        return $this->submissionGenerator;
    }
}
