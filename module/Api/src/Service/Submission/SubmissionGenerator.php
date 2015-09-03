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
}
