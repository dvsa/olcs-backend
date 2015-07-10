<?php

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Grant extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');
        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $messages = $this->getGrantMessages($application);
        $canGrant = empty($messages);

        return $this->result(
            $application,
            [],
            [
                'canGrant' => $canGrant,
                'reasons' => $messages,
                'canHaveInspectionRequest' => $canGrant && $this->canHaveInspectionRequest($application)
            ]
        );
    }

    protected function getGrantMessages(ApplicationEntity $application)
    {
        if ($application->isVariation()) {
            return $this->getGrantVariationMessages($application);
        }

        return $this->getGrantApplicationMessages($application);
    }

    protected function getGrantApplicationMessages(ApplicationEntity $application)
    {
        $errors = [];
        $accessible = $this->getAccessibleSections($application);

        if (!$application->getApplicationTracking()->isValid($accessible)) {
            $errors['application-grant-error-tracking'] = 'application-grant-error-tracking';
        }

        $required = $this->getRequiredApplicationSections($application);

        if (!$application->getApplicationCompletion()->isComplete($required)) {
            $missingSections = $application->getApplicationCompletion()->getIncompleteSections($required);
            $errors['application-grant-error-sections'] = $missingSections;
        }

        if (!$this->feeStatusIsValid($application)) {
            $errors['application-grant-error-fees'] = 'application-grant-error-fees';
        }

        // check enforcement area status
        if ($this->shouldValidateEnforcementArea($application)
            && !$this->enforcementAreaIsValid($application)
        ) {
            $errors['application-grant-error-enforcement-area'] = 'application-grant-error-enforcement-area';
        }

        return $errors;
    }

    protected function getGrantVariationMessages(ApplicationEntity $application)
    {
        $errors = [];
        $accessible = $this->getAccessibleSections($application);

        if (!$application->getApplicationTracking()->isValid($accessible)) {
            $errors['application-grant-error-tracking'] = 'application-grant-error-tracking';
        }

        if (!$application->hasVariationChanges()) {
            $errors['variation-grant-error-no-change'] = 'variation-grant-error-no-change';
        } else {
            $incompleteSections = $application->getSectionsRequiringAttention();

            if (!empty($incompleteSections)) {
                $errors['variation-grant-error-sections'] = $incompleteSections;
            }
        }

        // check fee status
        if (!$this->feeStatusIsValid($application)) {
            $errors['application-grant-error-fees'] = 'application-grant-error-fees';
        }

        return $errors;
    }

    protected function getAccessibleSections(ApplicationEntity $application)
    {
        $accessible = $this->sectionAccessService->getAccessibleSections($application);
        return array_keys($accessible);
    }

    protected function shouldValidateEnforcementArea(ApplicationEntity $application)
    {
        // don't validate enforcement area if PSV special restricted
        return ($application->isGoods() || !$application->isSpecialRestricted());
    }

    protected function enforcementAreaIsValid(ApplicationEntity $application)
    {
        return $application->getLicence()->getEnforcementArea() !== null;
    }

    protected function feeStatusIsValid(ApplicationEntity $application)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(
            $criteria->expr()->in(
                'feeStatus',
                [
                    $this->getRepo()->getRefdataReference(Fee::STATUS_OUTSTANDING),
                    $this->getRepo()->getRefdataReference(Fee::STATUS_WAIVE_RECOMMENDED)
                ]
            )
        );

        // Get outstanding fees
        $fees = $application->getFees()->matching($criteria);

        /** @var Fee $fee */
        foreach ($fees as $fee) {
            if ($fee->getFeeType()->getFeeType()->getId() !== RefData::FEE_TYPE_GRANTINT) {
                $fees->removeElement($fee);
            }
        }

        return $fees->count() < 1;
    }

    protected function getRequiredApplicationSections(ApplicationEntity $application)
    {
        $requiredSections = [
            'type_of_licence',
            'business_type',
            'business_details',
            'addresses',
            'people',
        ];
        if ($application->isSpecialRestricted()) {
            $requiredSections[] = 'taxi_phv';
        } else {
            $requiredSections[] = 'operating_centres';
        }

        return $requiredSections;
    }

    protected function canHaveInspectionRequest(ApplicationEntity $application)
    {
        return !$application->isVariation();
    }
}
