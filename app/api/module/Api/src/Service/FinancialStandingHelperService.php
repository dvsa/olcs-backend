<?php

/**
 * Financial Standing Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @var \Dvsa\Olcs\Api\Domain\Repository\FeeType
     */
    protected $feeTypeRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->ratesRepo = $serviceLocator->get('RepositoryServiceManager')->get('FinancialStandingRate');
        return $this;
    }

    /**
     * Takes an array of vehicle authorisations (example below) and
     * returns the required finance amount
     *
     * array (
     *   0 =>
     *   array (
     *     'category' => 'lcat_gv',
     *     'type' => 'ltyp_si',
     *     'count' => 3,
     *   ),
     *   1 =>
     *   array (
     *     'category' => 'lcat_gv',
     *     'type' => 'ltyp_r',
     *     'count' => 3,
     *   ),
     *   2 =>
     *   array (
     *     'category' => 'lcat_psv',
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
        foreach ($auths as $key => $auth) {
            if (!$foundHigher && $auth['count']>0) {
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
                return (float) $rate->getFirstVehicleRate();
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

    /**
     * Get rates in effect
     * @return array FinancialStandingRate[]
     */
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
