<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Submission\CreateSubmissionSectionComment;

/**
 * Class SubmissionCommentService
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class SubmissionCommentService
{
    private $sectionConfig;

    public function __construct($config)
    {
        $this->sectionConfig = $config;
    }

    /**
     * Returns an array of Comment commands set up with comment text generated from the section data
     *
     * @param SubmissionEntity $submissionEntity
     * @return array
     */
    public function generateCommentCommands(SubmissionEntity $submissionEntity)
    {
        $commentCommands = [];
        $allSelectedSectionData = $submissionEntity->getSectionData();

        // foreach section chosen
        foreach ($allSelectedSectionData as $selectedSectionId => $selectedSectionData) {

            // get the config for that section
            $sectionConfig = $this->sectionConfig[$selectedSectionId];

            if (!empty($sectionConfig)) {

                // if section config entry contains 'text', generate comment based on value of text stored against the
                // section
                if (in_array('text', $sectionConfig['section_type']) && isset($selectedSectionData['data']['text'])) {
                    array_push(
                        $commentCommands,
                        CreateSubmissionSectionComment::create(
                            [
                                'id' => '',
                                'submission' => $submissionEntity->getId(),
                                'submissionSection' => $selectedSectionId,
                                'comment' => $selectedSectionData['data']['text'],
                            ]
                        )
                    );
                }
            }
        }

        return $commentCommands;
    }
}
