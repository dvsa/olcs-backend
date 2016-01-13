<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;

/**
 * SubmissionGeneratorAwareInterface
 */
interface SubmissionGeneratorAwareInterface
{
    /**
     * @param SubmissionGenerator $service
     */
    public function setSubmissionGenerator(SubmissionGenerator $service);

    /**
     * @return SubmissionGenerator
     */
    public function getSubmissionGenerator();

    /**
     * @param array $submissionConfig
     */
    public function setSubmissionConfig(array $submissionConfig);

    /**
     * @return array submission configuration
     */
    public function getSubmissionConfig();
}
