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
    private $sectionGeneratorPluginManager;

    public function __construct($config, $sectionGeneratorPluginManager)
    {
        $this->submissionConfig = $config;
        $this->sectionGeneratorPluginManager = $sectionGeneratorPluginManager;
    }

    public function generateSubmission(SubmissionEntity $submissionEntity, $sections)
    {
        $sectionTypeId = $submissionEntity->getSubmissionType()->getId();

        if (!isset($this->submissionConfig['section-types'][$sectionTypeId])) {
            throw new \Exception('Invalid submission type');
        }

        $requiredSections = $this->getRequiredSections($sectionTypeId, $sections);

        // foreach section
        foreach ($requiredSections as $sectionId) {
            $data = $this->sectionGeneratorPluginManager
                ->get($sectionId)
                ->generateSection(
                    $submissionEntity->getCase()
                );

            $submissionEntity->setSectionData($sectionId, $data);
        }

        $submissionEntity->setSubmissionDataSnapshot();

        return $submissionEntity;
    }

    private function getRequiredSections($sectionTypeId, $postedSections)
    {
        $submissionTypeSections = $this->submissionConfig['section-types'][$sectionTypeId];
        return array_unique(array_merge($submissionTypeSections, $postedSections));
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
        $sectionTypeId = $submissionEntity->getSubmissionType()->getId();
        $submissionTypeSections = $this->submissionConfig['section-types'][$sectionTypeId];

        $requiredSections = array_unique(array_merge($submissionTypeSections, $sections));

        $context = new \ArrayObject($requiredSections);

        foreach ($requiredSections as $sectionId) {
            $contextClass = $this->submissionConfig['sections'][$sectionId]['context'];
            $this->submissionContextManager->get($contextClass)->provide($submissionEntity, $context);
        }

        $contextArray = $context->getArrayCopy();
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
