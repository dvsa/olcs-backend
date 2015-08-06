<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;

/**
 * Class SubmissionGenerator
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class SubmissionGenerator
{
    private $submissionConfig;
    private $submissionContextManager;
    private $submissionProcessManager;

    public function __construct($config, ServiceLocatorInterface $context, ServiceLocatorInterface $process)
    {
        $this->submissionConfig = $config;
        $this->submissionContextManager = $context;
        $this->submissionProcessManager = $process;
    }

    public function generateSubmissionData(SubmissionEntity $submissionEntity, $sections)
    {

        if (!isset($this->submissionConfig[$submissionEntity->getSubmissionType()->getId()])) {
            throw new \Exception('Invalid submission type');
        }

        /** @var \ArrayObject $contextObject object representing mandatory sections for the type plus those selected */
        $contextObject = $this->fetchContext(
            $submissionEntity,
            $sections
        );

        return $this->processSubmission($this->submissionConfig[$submissionTypeKey], $submissionEntity, $contextObject);
    }

    /**
     *
     * @param string $submissionTypeKey
     * @param SubmissionEntity $submission
     * @param array $sections
     * @return ImmutableArrayObject
     */
    private function fetchContext(SubmissionEntity $submissionEntity, $sections)
    {
        $submissionTypeSections = $this->submissionConfig[$submissionEntity->getSubmissionType()->getId()];

        $requiredSections = array_merge($submissionTypeSections, $sections);
        var_dump($requiredSections);exit;
        $context = new \ArrayObject($requiredSections);

        if (isset($config['context'])) {
            foreach ($config['context'] as $contextClass) {
                $this->submissionContextManager->get($contextClass)->provide($submission, $context);
            }
        }

        $contextArray = $context->getArrayCopy();
var_dump($contextArray);exit;
        return new ImmutableArrayObject($contextArray);
    }

    private function processSubmission($config, SubmissionEntity $submission, $context)
    {
        if (!isset($config['process'])) {
            throw new \Exception('No submission processors specified');
        }

        foreach ($config['process'] as $process) {
            $this->submissionProcessManager->get($process)->process($submission, $context);
        }

        return $submission;
    }
}
