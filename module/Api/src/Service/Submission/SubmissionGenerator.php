<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;

/**
 * Class SubmissionGenerator - Responsible for generating the submission section data and storing the json encoded data
 * snapshot against the submission entity
 *
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class SubmissionGenerator
{
    private $submissionConfig;
    private $sectionGeneratorPluginManager;

    /**
     * Construct the submission generator
     *
     * @param $config
     * @param $sectionGeneratorPluginManager
     */
    public function __construct($config, $sectionGeneratorPluginManager)
    {
        $this->submissionConfig = $config;
        $this->sectionGeneratorPluginManager = $sectionGeneratorPluginManager;
    }

    /**
     * Generates a submission after first determining sections to generate and sets it against the submission entity
     *
     * @param SubmissionEntity $submissionEntity
     * @param $sections
     * @return SubmissionEntity
     * @throws \Exception
     */
    public function generateSubmission(SubmissionEntity $submissionEntity, $sections)
    {
        $isTm = $submissionEntity->getCase()->isTm();

        $sectionTypeId = $submissionEntity->getSubmissionType()->getId();

        if (!isset($this->submissionConfig['section-types'][$sectionTypeId])) {
            throw new \Exception('Invalid submission type');
        }

        $requiredSections = $this->getRequiredSections($sectionTypeId, $sections, $isTm);

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

    /**
     * Generates the snapshot data for a given sectionId by calling it's relevent section handler object
     *
     * @param SubmissionEntity $submissionEntity
     * @param $sectionId
     * @param null $subSection
     * @return mixed
     */
    public function generateSubmissionSectionData(SubmissionEntity $submissionEntity, $sectionId, $subSection = null)
    {
        $data = $this->sectionGeneratorPluginManager
            ->get($sectionId)
            ->generateSection(
                $submissionEntity->getCase()
            );

        if (!empty($subSection) && isset($data[$subSection])) {
            return $data[$subSection];
        }

        return $data;
    }

    /**
     * Uses default list for submission type, along with those posted in request and returns a list of sections
     * to generate
     *
     * @param $sectionTypeId
     * @param $postedSections
     * @param $isTm
     * @return array|mixed
     */
    private function getRequiredSections($sectionTypeId, $postedSections, $isTm)
    {
        $submissionTypeSections = $this->submissionConfig['section-types'][$sectionTypeId];

        $sections = array_unique(array_merge($submissionTypeSections, $postedSections));

        if ($isTm) {
            $sectionsToRemove = ['case-summary', 'outstanding-applications', 'people'];
            foreach ($sectionsToRemove as $sectionToRemove) {
                $sections = $this->removeSection($sections, $sectionToRemove);
            }
        }

        return $sections;
    }

    /**
     * Remove a section (sectionToRemove) from array $sections
     *
     * @param $sections
     * @param $sectionToRemove
     * @return mixed
     */
    private function removeSection($sections, $sectionToRemove)
    {
        if (($key = array_search($sectionToRemove, $sections)) !== false) {
            unset($sections[$key]);
        }
        return $sections;
    }
}
