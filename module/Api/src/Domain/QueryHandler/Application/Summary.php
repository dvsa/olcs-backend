<?php

/**
 * Summary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Utils\Helper\ValueHelper;

/**
 * Summary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Summary extends AbstractQueryHandler
{
    const ACTION_PRINT_SIGN_RETURN = 'PRINT_SIGN_RETURN';
    const ACTION_SUPPLY_SUPPORTING_EVIDENCE = 'SUPPLY_SUPPORTING_EVIDENCE';
    const ACTION_APPROVE_TM = 'APPROVE_TM';

    const MISSING_EVIDENCE_OC = 'MISSING_EVIDENCE_OC';
    const MISSING_EVIDENCE_FINANCIAL = 'markup-financial-standing-proof';

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee', 'SystemParameter'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $actions = $this->determineActions($application);

        $bundle = [
            'licence',
            'status'
        ];

        if (array_key_exists(self::ACTION_APPROVE_TM, $actions)) {
            $bundle['transportManagers'] = [
                'tmApplicationStatus',
                'transportManager' => [
                    'homeCd' => [
                        'person'
                    ]
                ]
            ];

            if ($application->isVariation()) {
                $criteria = Criteria::create();
                $criteria->where(
                    $criteria->expr()->in(
                        'action',
                        [
                            Entity\Application\ApplicationOperatingCentre::ACTION_ADD,
                            Entity\Application\ApplicationOperatingCentre::ACTION_UPDATE
                        ]
                    )
                );

                $bundle['transportManagers']['criteria'] = $criteria;
            }
        }

        $reference = $this->getLatestReference($application->getId());

        return $this->result(
            $application,
            $bundle,
            [
                'actions' => $actions,
                'reference' => $reference,
                'outstandingFee' => $application->getLatestOutstandingApplicationFee() !== null,
            ]
        );
    }

    protected function determineActions(Entity\Application\Application $application)
    {
        $actions = [];

        if ($this->needsToSign($application)) {
            $actions[self::ACTION_PRINT_SIGN_RETURN] = self::ACTION_PRINT_SIGN_RETURN;
        }

        $missingEvidence = $this->determineMissingEvidence($application);
        if (!empty($missingEvidence)) {
            $actions[self::ACTION_SUPPLY_SUPPORTING_EVIDENCE] = $missingEvidence;
        }

        if ($this->needsToApproveTms($application)) {
            $actions[self::ACTION_APPROVE_TM] = self::ACTION_APPROVE_TM;
        }

        return $actions;
    }

    protected function needsToSign(Entity\Application\Application $application)
    {
        if ($application->isVariation()) {
            return false;
        }

        if (ValueHelper::isOn($application->getAuthSignature())) {
            return false;
        }

        return true;
    }

    protected function determineMissingEvidence(Entity\Application\Application $application)
    {
        if ($application->getLicenceType()->getId() === Entity\Licence\Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return [];
        }
        $evidence = [];

        if ($this->isMissingOcDocuments($application)) {
            $evidence[] = self::MISSING_EVIDENCE_OC;
        }

        if ($this->isMissingFinancialEvidence($application)) {
            $evidence[] = self::MISSING_EVIDENCE_FINANCIAL;
        }

        return $evidence;
    }

    protected function isMissingOcDocuments(Entity\Application\Application $application)
    {
        if ($application->isPsv()) {
            return false;
        }

        $ocs = $this->getAocsToCheck($application);

        // If there are no OCs then we can return false
        if ($ocs->isEmpty()) {
            return false;
        }

        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('application', $application));

        /** @var Entity\Application\ApplicationOperatingCentre $aoc */
        foreach ($ocs as $aoc) {

            if (ValueHelper::isOn($aoc->getAdPlaced())) {
                continue;
            }

            if ($this->doesAocRequireDocs($application, $aoc)) {
                return true;
            }
        }

        return false;
    }

    protected function doesAocRequireDocs(
        Entity\Application\Application $application,
        Entity\Application\ApplicationOperatingCentre $aoc
    ) {
        // If we are not updating the OC, then we definitely need some docs, so we need to return here
        if ($aoc->getAction() !== Entity\Application\ApplicationOperatingCentre::ACTION_UPDATE) {
            return true;
        }

        // If we are updating the record, we need to see if we have increased auth
        $loc = $application->getLicence()->getLocByOc($aoc->getOperatingCentre());

        return (
            $aoc->getNoOfVehiclesRequired() > $loc->getNoOfVehiclesRequired()
            || $aoc->getNoOfTrailersRequired() > $loc->getNoOfTrailersRequired()
        );
    }

    /**
     * @param Entity\Application\Application $application
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection|static
     */
    protected function getAocsToCheck(Entity\Application\Application $application)
    {
        $ocs = $application->getOperatingCentres();

        // Filter to just add/edit records for variation
        if ($application->isVariation()) {
            $criteria = Criteria::create();
            $criteria->where($criteria->expr()->in('action', ['A', 'U']));

            $ocs = $ocs->matching($criteria);
        }

        return $ocs;
    }

    protected function isMissingFinancialEvidence(Entity\Application\Application $application)
    {
        $updated = Entity\Application\Application::VARIATION_STATUS_UPDATED;

        // If the application is a variation and the financial evidence section hasn't been updated, then we don't need
        // evidence
        if ($application->isVariation()
            && $application->getApplicationCompletion()->getFinancialEvidenceStatus() !== $updated) {
            return false;
        }

        $appCategory = $this->getRepo()->getCategoryReference(Entity\System\Category::CATEGORY_APPLICATION);
        $digitalCategory = $this->getRepo()->getSubCategoryReference(
            Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
        );

        $docs = $application->getApplicationDocuments($appCategory, $digitalCategory);

        if ($docs->isEmpty() === false) {
            return false;
        }

        $assistedDigitalCategory = $this->getRepo()->getSubCategoryReference(
            Entity\System\Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_ASSISTED_DIGITAL
        );

        $docs = $application->getApplicationDocuments($appCategory, $assistedDigitalCategory);

        if ($docs->isEmpty() === false) {
            return false;
        }

        return true;
    }

    protected function needsToApproveTms(Entity\Application\Application $application)
    {
        if ($application->getLicenceType()->getId() === Entity\Licence\Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return false;
        }
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->notIn(
                'tmApplicationStatus',
                [
                    Entity\Tm\TransportManagerApplication::STATUS_OPERATOR_SIGNED,
                    Entity\Tm\TransportManagerApplication::STATUS_RECEIVED
                ]
            )
        );

        if ($application->isVariation()) {
            $criteria->andWhere($criteria->expr()->in('action', ['A', 'U']));
        }

        $tms = $application->getTransportManagers()->matching($criteria);

        return $tms->isEmpty() === false;
    }

    protected function getLatestReference($applicationId)
    {
        $latestFee = $this->getRepo('Fee')->fetchLatestFeeByApplicationId($applicationId);
        if ($latestFee) {
            return $latestFee->getLatestPaymentRef();
        }
        return '';
    }
}
