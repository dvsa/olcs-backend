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
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $rates;

    public function handleQuery(QueryInterface $query)
    {
        $applicationRepo = $this->getRepo();
        $application = $applicationRepo->fetchUsingId($query, Query::HYDRATE_OBJECT);

        // var_dump($application->getLicence()->getId());

        $data = $application->jsonSerialize();

        $data['financialEvidence'] = array_merge(
            [
                'requiredFinance' => $this->getRequiredFinance($application),
                'vehicles' => $this->getTotalNumberOfAuthorisedVehicles($application),
            ],
            $this->getRatesForView($application)
        );

        return $data;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);

        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->ratesRepo = $mainServiceLocator->get('RepositoryServiceManager')->get('FinancialStandingRate');
        $this->licenceRepo = $mainServiceLocator->get('RepositoryServiceManager')->get('Licence');

        return $this;
    }


    protected function getRequiredFinance($application)
    {
        // add the application count
        $auths[] = [
            'type' => $application->getLicenceType()->getId(),
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
                        Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        Application::APPLICATION_STATUS_GRANTED,
                    ]
                )
                &&
                $app->getId() !== $application->getId()
            ) {
                $auths[] = [
                    'type' => $app->getLicenceType()->getId(),
                    'count' => $app->getTotAuthVehicles(),
                    'category' => $app->getGoodsOrPsv()->getId(),
                ];
            }
        }

        return $this->getFinanceCalculation($auths);

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
        $organisation = $application->getLicence()->getOrganisation();
        $applications = $this->getRepo()->fetchForOrganisation($organisation->getId());
        $filtered = [];
        foreach ($applications as $app) {
             if (
                in_array(
                    $app->getStatus()->getId(),
                    [
                        Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        Application::APPLICATION_STATUS_GRANTED,
                    ]
                )
                &&
                $app->getId() !== $application->getId()
            ) {
                $filtered[] = $app;
            }
        }

        return $filtered;
    }

    /**
     * Takes an array of vehicle authorisations (example below) and
     * returns the required finance amount
     *
     * array (
     *   0 =>
     *   array (
     *     'category' => 'lcat_gv'
     *     'type' => 'ltyp_si',
     *     'count' => 3,
     *   ),
     *   1 =>
     *   array (
     *     'category' => 'lcat_gv'
     *     'type' => 'ltyp_r',
     *     'count' => 3,
     *   ),
     *   2 =>
     *   array (
     *     'catgegory' => 'lcat_psv'
     *     'type' => 'ltyp_r',
     *     'count' => 1,
     *   ),
     * )
     *
     * Calculation:
     *    1 x 7000
     * +  2 x 3900
     * +  3 x 1700
     * +  1 x 2700
     * -----------
     *       22600
     *
     * @param array $auths
     * @return int
     */
    protected function getFinanceCalculation(array $auths)
    {
        $firstVehicleCharge      = 0;
        $additionalVehicleCharge = 0;
        $foundHigher             = false;
        $higherChargeTypes       = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
        ];

        // 1. Sort the array so the correct (higher) 'first vehicle' charge is
        // applied (i.e. ensure any PSV apps/licences are handled first)
        usort(
            $auths,
            function ($a, $b) {
                return $a['category'] === Licence::LICENCE_CATEGORY_PSV ? -1 : 1;
            }
        );

        // 2. Get first vehicle charge
        foreach ($auths as $key => $auth) {
            if (!$foundHigher && $count = $auth['count']>0) {
                $firstVehicleCharge = $this->getFirstVehicleRate($auth['type'], $auth['category']);
                $firstVehicleKey = $key;
            }
            if (in_array($auth['type'], $higherChargeTypes)) {
                $foundHigher = true;
            }
        }

        // 3. Ensure we don't double-count the first vehicle
        $auths[$firstVehicleKey]['count']--;

        // 4. Get the additional vehicle charges
        foreach ($auths as $key => $auth) {
            $rate = $this->getAdditionalVehicleRate($auth['type'], $auth['category']);
            $additionalVehicleCharge += ($auth['count'] * $rate);
        }

        // 5. Return the total required finance
        return $firstVehicleCharge + $additionalVehicleCharge;
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return float
     */
    public function getFirstVehicleRate($licenceType, $goodsOrPsv)
    {
        foreach ($this->getRates() as $rate) {
            if ($rate->getGoodsOrPsv()->getId() == $goodsOrPsv && $rate->getLicenceType()->getId() == $licenceType) {
                return (float) $rate->getFirstVehicleRate();;
            }
        }
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return float
     */
    public function getAdditionalVehicleRate($licenceType, $goodsOrPsv)
    {
        foreach ($this->getRates() as $rate) {
            if ($rate->getGoodsOrPsv()->getId() == $goodsOrPsv && $rate->getLicenceType()->getId() == $licenceType) {
                return (float) $rate->getAdditionalVehicleRate();
            }
        }
    }

    protected function getRates()
    {
        // we only make one call to look up standing rates
        if (is_null($this->rates)) {
            $date = new \DateTime();
            $this->rates = $this->ratesRepo->getRatesInEffect($date);
        }
        return $this->rates;
    }

    /**
     * Gets the vehicle rates to display in the help section of the page. Note
     * that currently we only display the rates according to the current
     * application category (Goods or PSV) - if the operator holds another
     * category of licence those figures will be used for the calculation but
     * are not shown.
     *
     * @param int $applicationId
     * @return array
     */
    public function getRatesForView($application)
    {
        $goodsOrPsv = $application->getGoodsOrPsv()->getId();

        return [
            'standardFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                $goodsOrPsv
            ),
            'standardAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                $goodsOrPsv
            ),
            'restrictedFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_RESTRICTED,
                $goodsOrPsv
            ),
            'restrictedAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_RESTRICTED,
                $goodsOrPsv
            ),
        ];
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
