<?php

namespace Dvsa\Olcs\Api\Service;

use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Financial Standing Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialStandingHelperService implements FactoryInterface
{
    /**
     * @var array $rates
     */
    protected $rates;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate
     */
    protected $ratesRepo;

    /**
     * @var Organisation
     */
    protected $organisationRepo;

    /**
     * @var Application
     */
    protected $applicationRepo;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service manaager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->ratesRepo = $serviceLocator->get('RepositoryServiceManager')->get('FinancialStandingRate');
        $this->organisationRepo = $serviceLocator->get('RepositoryServiceManager')->get('Organisation');
        $this->applicationRepo = $serviceLocator->get('RepositoryServiceManager')->get('Application');
        return $this;
    }

    /**
     * Get the amount of required finance for an organisation
     *
     * @param int $organisationId Organisation ID
     *
     * @return int Amount in pounds
     */
    public function getFinanceCalculationForOrganisation($organisationId)
    {
        $auths = [];
        /** @var \Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation */
        $organisation = $this->organisationRepo->fetchById($organisationId);

        $applications = $this->applicationRepo->fetchActiveForOrganisation($organisation->getId());
        if (!empty($applications)) {
            foreach ($applications as $app) {
                // filter new apps only
                if ($app->isVariation()) {
                    continue;
                }
                $auths[] = [
                    'type' => $app->getLicenceType()->getId(),
                    'count' => $app->getTotAuthVehicles(),
                    'category' => $app->getGoodsOrPsv()->getId(),
                ];
            }
        }

        $licences = $organisation->getActiveLicences();
        if (!empty($licences)) {
            foreach ($licences as $licence) {
                $auths[] = [
                    'type' => $licence->getLicenceType()->getId(),
                    'count' => $licence->getTotAuthVehicles(),
                    'category' => $licence->getGoodsOrPsv()->getId(),
                ];
            }
        }

        return $this->getFinanceCalculation($auths);
    }

    /**
     * Takes an array of vehicle authorisations (example below) and
     * returns the required finance amount
     *
     * array (
     *   0 => array (
     *     'category' => 'lcat_gv',
     *     'type' => 'ltyp_si',
     *     'count' => 3,
     *   ),
     *   1 => array (
     *     'category' => 'lcat_gv',
     *     'type' => 'ltyp_r',
     *     'count' => 3,
     *   ),
     *   2 => array (
     *     'category' => 'lcat_psv',
     *     'type' => 'ltyp_r',
     *     'count' => 1,
     *   ),
     * )
     *
     * Calculation:
     *    1 x 7000  first vehicle rate GV/SI
     * +  2 x 3900  additional vehicle rate GV/SI
     * +  3 x 1700  additional vehicle rate GV/R
     * +  1 x 2700  additional vehicle rate PSV/R
     * -----------
     *       22600
     *
     * @param array $auths
     * @return int
     */
    public function getFinanceCalculation(array $auths)
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
                unset($b); // not used in comparison
                return $a['category'] === Licence::LICENCE_CATEGORY_PSV ? -1 : 1;
            }
        );

        // 2. Get first vehicle charge
        $firstVehicleKey = null;
        foreach ($auths as $key => $auth) {
            if (!$foundHigher && $auth['count']>0) {
                $firstVehicleCharge = $this->getFirstVehicleRate($auth['type'], $auth['category']);
                $firstVehicleKey = $key;
                if (in_array($auth['type'], $higherChargeTypes, true)) {
                    $foundHigher = true;
                }
            }
        }

        // 3. Ensure we don't double-count the first vehicle
        if ($firstVehicleKey !== null) {
            $auths[$firstVehicleKey]['count']--;
        }

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
                return (float) $rate->getFirstVehicleRate();
            }
        }

        return null;
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

        return null;
    }

    /**
     * Get rates in effect
     * @return array FinancialStandingRate[]
     */
    protected function getRates()
    {
        // we only make one call to look up standing rates
        if (is_null($this->rates)) {
            $date = new \DateTime();
            $this->rates = $this->ratesRepo->fetchRatesInEffect($date);
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
     * @param string $goodsOrPsv
     * @return array
     */
    public function getRatesForView($goodsOrPsv)
    {
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
}
