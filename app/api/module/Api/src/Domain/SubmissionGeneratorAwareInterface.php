<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Submission\SubmissionCommentService;
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
    public function setSubmissionConfig($submissionConfig);

    /**
     * @return array submission configuration
     */
    public function getSubmissionConfig();

    /**
     * @param SubmissionCommentService $submissionConfigService
     * @return mixed
     */
    public function setSubmissionCommentService(SubmissionCommentService $submissionConfigService);

    /**
     * @return SubmissionCommentService $submissionConfigService
     */
    public function getSubmissionCommentService();
}
