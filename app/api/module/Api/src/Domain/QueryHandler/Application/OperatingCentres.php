<?php

/**
 * Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficAreaEnforcementArea;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;

/**
 * Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentres extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOperatingCentre', 'TrafficArea', 'Document'];

    /**
     * @var VariationOperatingCentreHelper
     */
    private $variationHelper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->variationHelper = $serviceLocator->getServiceLocator()->get('VariationOperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [
                'licence' => [
                    'trafficArea',
                    'enforcementArea'
                ],
            ],
            [
                'requiresVariation' => false,
                'operatingCentres' => $this->getAocData($application, $query),
                'totCommunityLicences' => $this->getTotCommunityLicences($application),
                'isPsv' => $application->isPsv(),
                'canHaveCommunityLicences' => $application->canHaveCommunityLicences(),
                'canHaveSchedule41' => $this->canHaveSchedule41($application),
                'possibleEnforcementAreas' => $this->getPossibleEnforcementAreas($application),
                'possibleTrafficAreas' => $this->getPossibleTrafficAreas($application),
                // Vars used for add form
                'canAddAnother' => $this->canAddAnother($application),
                'documents' => $this->resultList(
                    $this->getRepo('Document')->fetchUnlinkedOcDocumentsForEntity($application)
                )
            ]
        );
    }

    protected function canAddAnother(ApplicationEntity $application)
    {
        return !($application->isNew()
            && $application->getNiFlag() === 'N'
            && $application->getLicence()->getTrafficArea() === null
        );
    }

    protected function canHaveSchedule41(ApplicationEntity $application)
    {
        if ($this->isGranted(Permission::SELFSERVE_USER)) {
            return false;
        }

        if ($application->isPsv()) {
            return false;
        }

        if ($application->getStatus()->getId() !== ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION) {
            return false;
        }

        if (count($application->getActiveS4s()) > 0) {
            return false;
        }

        return true;
    }

    protected function getPossibleTrafficAreas(ApplicationEntity $application)
    {
        return $this->getRepo('TrafficArea')->getValueOptions();
    }

    protected function getPossibleEnforcementAreas(ApplicationEntity $application)
    {
        if ($application->getLicence()->getTrafficArea() === null) {
            return [];
        }

        /** @var TrafficAreaEnforcementArea[] $tas */
        $tas = $application->getLicence()->getTrafficArea()->getTrafficAreaEnforcementAreas();

        $options = [];
        foreach ($tas as $ta) {
            $options[$ta->getEnforcementArea()->getId()] = $ta->getEnforcementArea()->getName();
        }

        return $options;
    }

    protected function getTotCommunityLicences(ApplicationEntity $application)
    {
        if ($application->isVariation()) {
            return $application->getLicence()->getTotCommunityLicences();
        }

        return $application->getTotCommunityLicences();
    }

    protected function getAocData(ApplicationEntity $application, $query)
    {
        if (!$application->isVariation()) {
            return $this->getRepo('ApplicationOperatingCentre')
                ->fetchByApplicationIdForOperatingCentres($application->getId(), $query);
        }

        return $this->variationHelper->getListDataForApplication($application, $query);
    }
}
