<?php

/**
 * Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;

/**
 * Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['FinancialStandingRate'];

    /**
     * @var \Dvsa\Olcs\Api\Service\CpmsHelperService $cpmsHelper
     */
    protected $helper;

    /**
     * @var array $rates
     */
    protected $rates;

    /**
     * @var array $otherApplications
     */
    protected $otherApplications;

    public function handleQuery(QueryInterface $query)
    {
        $applicationRepo = $this->getRepo();
        $application = $applicationRepo->fetchUsingId($query, Query::HYDRATE_OBJECT);

        // add documents
        $financialDocuments = $application->getApplicationDocuments(
            $applicationRepo->getCategoryReference(Category::CATEGORY_APPLICATION),
            $applicationRepo->getSubCategoryReference(SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL)
        );

        // add calculated finance data
        $financialEvidence = array_merge(
            [
                'requiredFinance' => $this->getRequiredFinance($application),
                'vehicles' => $this->getTotalNumberOfAuthorisedVehicles($application),
            ],
            $this->helper->getRatesForView($application->getGoodsOrPsv()->getId())
        );

        $data = $application->jsonSerialize();
        $data['documents'] = $financialDocuments->toArray();
        $data['financialEvidence'] = $financialEvidence;

        return $data;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->helper = $serviceLocator->getServiceLocator()->get('FinancialStandingHelperService');
        return $this;
    }

    protected function getRequiredFinance($application)
    {
        // add the application count
        $type = null;
        if ($application->getLicenceType()) {
            $type = $application->getLicenceType()->getId();
        }
        $auths[] = [
            'type' => $type,
            'count' => $application->getTotAuthVehicles(),
            'category' =>  $application->getGoodsOrPsv()->getId(),
        ];

        // add the counts for each licence
        $licences = $this->getOtherLicences($application);
        foreach ($licences as $licence) {
            $auths[] = [
                'type' => $licence->getLicenceType()->getId(),
                'count' => $licence->getTotAuthVehicles(),
                'category' => $licence->getGoodsOrPsv()->getId(),
            ];
        }

        // add the counts for each other application
        $applications = $this->getOtherApplications($application);
        foreach ($applications as $app) {
            if (
                in_array(
                    $app->getStatus()->getId(),
                    [
                        ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        ApplicationEntity::APPLICATION_STATUS_GRANTED,
                    ]
                )
                &&
                $app->getId() !== $application->getId()
                &&
                !is_null($app->getGoodsOrPsv())
            ) {
                $type = null;
                if ($app->getLicenceType()) {
                    $type = $app->getLicenceType()->getId();
                }
                $auths[] = [
                    'type' => $type,
                    'count' => $app->getTotAuthVehicles(),
                    'category' => $app->getGoodsOrPsv()->getId(),
                ];
            }
        }

        return $this->helper->getFinanceCalculation($auths);
    }

    protected function getOtherLicences($application)
    {
        $organisation = $application->getLicence()->getOrganisation();
        $licences = $organisation->getLicences();
        $filtered = [];
        foreach ($licences as $licence) {
            if (
                in_array(
                    $licence->getStatus()->getId(),
                    [
                        Licence::LICENCE_STATUS_VALID,
                        Licence::LICENCE_STATUS_SUSPENDED,
                        Licence::LICENCE_STATUS_CURTAILED,
                    ]
                )
                &&
                $licence->getId() !== $application->getLicence()->getId()
            ) {
                $filtered[] = $licence;
            }
        }
        return $filtered;
    }

    protected function getOtherApplications($application)
    {
        if (is_null($this->otherApplications)) {
            $organisation = $application->getLicence()->getOrganisation();
            $applications = $this->getRepo()->fetchForOrganisation($organisation->getId());
            $this->otherApplications = [];
            foreach ($applications as $app) {
                if (
                    in_array(
                        $app->getStatus()->getId(),
                        [
                            ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                            ApplicationEntity::APPLICATION_STATUS_GRANTED,
                        ]
                    )
                    &&
                    $app->getId() !== $application->getId()
                ) {
                    $this->otherApplications[] = $app;
                }
            }
        }

        return $this->otherApplications;
    }

    /**
     * Get the total vehicle authority which includes:
     *
     *  The vehicles on this application
     *  PLUS the vehicles across other licence records with a status of:
     *   Valid
     *   Suspended
     *   Curtailed
     *  PLUS the vehicles across other new applications with a status of:
     *   Under consideration
     *   Granted
     *
     * @param int $applicationId
     * @return int
     */
    public function getTotalNumberOfAuthorisedVehicles($application)
    {
        // get the total vehicle authorisation for this application
        $appVehicles = $application->getTotAuthVehicles();

        // get the total vehicle authorisation for other licences
        $otherLicenceVehicles = 0;
        $licences = $this->getOtherLicences($application);
        foreach ($licences as $licence) {
            $otherLicenceVehicles += (int)$licence->getTotAuthVehicles();
        }

        // get the total vehicle authorisation for other applications
        // that are 'under consideration' or 'granted'
        $otherApplicationVehicles = 0;
        $applications = $this->getOtherApplications($application);
        foreach ($applications as $application) {
            $otherApplicationVehicles += (int)$application->getTotAuthVehicles();
        }

        return $appVehicles + $otherLicenceVehicles + $otherApplicationVehicles;
    }
}
