<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Submission\SubmissionCommentService;
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
     * @var array $submissionConfig
     */
    protected $submissionConfig;

    /**
     * @var SubmissionCommentService
     */
    protected $submissionCommentService;

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

    /**
     * @return array
     */
    public function getSubmissionConfig()
    {
        return $this->submissionConfig;
    }

    /**
     * @param array $submissionConfig
     */
    public function setSubmissionConfig($submissionConfig)
    {
        $this->submissionConfig = $submissionConfig;
    }

    /**
     * @return SubmissionCommentService
     */
    public function getSubmissionCommentService()
    {
        return $this->submissionCommentService;
    }

    /**
     * @param SubmissionCommentService $submissionCommentService
     */
    public function setSubmissionCommentService(SubmissionCommentService $submissionCommentService)
    {
        $this->submissionCommentService = $submissionCommentService;
    }
}
