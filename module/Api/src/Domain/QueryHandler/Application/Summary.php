<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\Surrender\OpenCases;
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

    protected $extraRepos = ['Fee', 'Cases'];

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Application\Summary $query Query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
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

        return $this->result(
            $application,
            $bundle,
            [
                'actions' => $actions,
                'reference' => $this->getLatestPaymentReference($application->getId()),
                'outstandingFee' => $application->getLatestOutstandingApplicationFee() !== null,
                'canWithdraw' => $this->canWithdraw($application)
            ]
        );
    }

    /**
     * Determine Actions
     *
     * @param Entity\Application\Application $application Application object
     *
     * @return array
     */
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

    /**
     * Define is application need to be signed
     *
     * @param Entity\Application\Application $application Application object
     *
     * @return bool
     */
    protected function needsToSign(Entity\Application\Application $application)
    {
        if ($application->isVariation()) {
            return false;
        }

        if (ValueHelper::isOn($application->getAuthSignature())) {
            return false;
        }

        if ($application->isDigitallySigned()) {
            return false;
        }

        return true;
    }

    /**
     * Determine missing Evidence
     *
     * @param Entity\Application\Application $application Application object
     *
     * @return array
     */
    protected function determineMissingEvidence(Entity\Application\Application $application)
    {
        if ($application->getLicenceType()->getId() === Entity\Licence\Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return [];
        }
        $evidence = [];

        if ($application->canAddOperatingCentresEvidence()) {
            $evidence[] = self::MISSING_EVIDENCE_OC;
        }

        if ($application->canAddFinancialEvidence()) {
            $evidence[] = self::MISSING_EVIDENCE_FINANCIAL;
        }

        return $evidence;
    }

    /**
     * Define is Needs To Approve Tms
     *
     * @param Entity\Application\Application $application Application object
     *
     * @return bool
     */
    private function needsToApproveTms(Entity\Application\Application $application)
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

    /**
     * Return reference number of latest payment
     *
     * @param int $appId Application Id
     *
     * @return null|string
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getLatestPaymentReference($appId)
    {
        /** @var Repository\Fee $repo */
        $repo = $this->getRepo('Fee');

        /** @var Entity\Fee\Fee $latestFee */
        $latestFee = $repo->fetchLatestPaidFeeByApplicationId($appId);
        if ($latestFee) {
            return $latestFee->getLatestPaymentRef();
        }

        return '';
    }

    private function canWithdraw(Entity\Application\Application $application)
    {
        $isUnderConsideration = ($application->getStatus()->getId() === $this->getRepo()->getRefdataReference($application::APPLICATION_STATUS_UNDER_CONSIDERATION));

        try {
            $openCases = $this->getRepo('Cases')->fetchOpenCasesForApplication($application->getLicence()->getId());

            if (count($openCases) > 0) {
                return false;
            }
        } catch (\Exception $nfe) {
            return $isUnderConsideration;
        }
        return $isUnderConsideration;
    }
}
