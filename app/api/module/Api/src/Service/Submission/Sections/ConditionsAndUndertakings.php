<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;

/**
 * Class ConditionsAndUndertakings
 *
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ConditionsAndUndertakings extends AbstractSection
{
    /**
     * Generate C's and U's attached to both licence and application. Sorted.
     *
     * @param CasesEntity $case Case relating to the submission
     *
     * @return array Data array containing information for the submission section
     */
    public function generateSection(CasesEntity $case)
    {
        $tables = ['undertakings' => [], 'conditions' => []];

        // CUs attached to the case
        $caseConditionsAndUndertakings = $case->getConditionUndertakingsAddedViaCase();
        if (!empty($caseConditionsAndUndertakings)) {
            /** @var ConditionUndertaking $entity */
            foreach ($caseConditionsAndUndertakings as $entity) {
                $tables[$this->determineTableName($entity)][] =
                    $this->generateTableRow($entity, $entity->getCase()->getId());
            }
        }

        // CUs attached to the any other applications for this licence
        $applications = $case->getLicence()->getApplications();

        if (!empty($applications)) {
            /** @var Application $application */
            foreach ($applications as $application) {
                /** @var ConditionUndertaking $entity */
                foreach ($application->getConditionUndertakings() as $entity) {
                    $tables[$this->determineTableName($entity)][] =
                        $this->generateTableRow($entity, $application->getId());
                }
            }
        }

        // CUs attached to the licence
        $licenceConditionsUndertakings = $case->getLicence()->getConditionUndertakingsAddedViaLicence();
        foreach ($licenceConditionsUndertakings as $entity) {
            $tables[$this->determineTableName($entity)][] =
                $this->generateTableRow($entity, $case->getLicence()->getLicNo());
        }

        usort(
            $tables['undertakings'],
            function ($a, $b) {
                return strtotime($b['createdOn']) - strtotime($a['createdOn']);
            }
        );
        usort(
            $tables['conditions'],
            function ($a, $b) {
                return strtotime($b['createdOn']) - strtotime($a['createdOn']);
            }
        );

        $dataToReturnArray = [
            'data' => [
                'tables' => $tables
            ]
        ];

        return $dataToReturnArray;
    }

    /**
     * Determine table name
     *
     * @param ConditionUndertaking $entity ConditionUndertaking entity being processed
     *
     * @return string
     */
    private function determineTableName(ConditionUndertaking $entity)
    {
        return $entity->getConditionType()->getId() == 'cdt_und' ? 'undertakings' : 'conditions';
    }

    /**
     * Generate a table row for this section
     *
     * @param ConditionUndertaking $entity   Entity being processed
     * @param string               $parentId Parent Id
     *
     * @return array
     */
    private function generateTableRow(ConditionUndertaking $entity, $parentId = '')
    {
        /** @var ConditionUndertaking $entity */
        $thisEntity = array();
        $thisEntity['id'] = $entity->getId();
        $thisEntity['version'] = $entity->getVersion();
        $thisEntity['createdOn'] = $this->formatDate($entity->getCreatedOn());
        $thisEntity['parentId'] = $parentId;
        $thisEntity['addedVia'] = $entity->getAddedVia()->getDescription();
        $thisEntity['isFulfilled'] = $entity->getIsFulfilled();
        $thisEntity['isDraft'] = $entity->getIsDraft();
        $thisEntity['attachedTo'] = $entity->getAttachedTo()->getDescription();
        $thisEntity['notes'] = $entity->getNotes();

        if (empty($entity->getOperatingCentre())) {
            $thisEntity['OcAddress'] = [];
        } else {
            $thisEntity['OcAddress'] = $entity->getOperatingCentre()->getAddress()->toArray();
        }

        return $thisEntity;
    }
}
