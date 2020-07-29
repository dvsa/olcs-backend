<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\Olcs\Api\Service\Permits\Common\TypeBasedPermitTypeConfigProvider;

class RangeBasedRestrictedCountriesProvider
{
    /** @var IrhpPermitRangeRepo */
    private $irhpPermitRangeRepo;

    /** @var TypeBasedPermitTypeConfigProvider */
    private $typeBasedPermitTypeConfigProvider;

    /** @var CountryRepo */
    private $countryRepo;

    /** @var array */
    private $restrictedCountries = [];

    /**
     * Create service instance
     *
     * @param IrhpPermitRangeRepo $irhpPermitRangeRepo
     * @param TypeBasedPermitTypeConfigProvider $typeBasedPermitTypeConfigProvider
     * @param CountryRepo $countryRepo
     *
     * @return RangeBasedRestrictedCountriesProvider
     */
    public function __construct(
        IrhpPermitRangeRepo $irhpPermitRangeRepo,
        TypeBasedPermitTypeConfigProvider $typeBasedPermitTypeConfigProvider,
        CountryRepo $countryRepo
    ) {
        $this->irhpPermitRangeRepo = $irhpPermitRangeRepo;
        $this->typeBasedPermitTypeConfigProvider = $typeBasedPermitTypeConfigProvider;
        $this->countryRepo = $countryRepo;
    }

    /**
     * Return the restricted countries
     *
     * @param int $irhpPermitRangeId
     *
     * @return array
     */
    public function getList($irhpPermitRangeId)
    {
        if (!isset($this->restrictedCountries[$irhpPermitRangeId])) {
            // fetch the list by ids
            $this->restrictedCountries[$irhpPermitRangeId] = $this->countryRepo->fetchByIds(
                $this->getIds($irhpPermitRangeId),
                Query::HYDRATE_ARRAY
            );
        }

        return $this->restrictedCountries[$irhpPermitRangeId];
    }

    /**
     * Return the restricted country ids
     *
     * @param int $irhpPermitRangeId
     *
     * @return array
     */
    private function getIds($irhpPermitRangeId)
    {
        $irhpPermitRange = $this->irhpPermitRangeRepo->fetchById($irhpPermitRangeId);

        // get ids of restricted countries based on type
        $irhpPermitTypeId = $irhpPermitRange->getIrhpPermitStock()->getIrhpPermitType()->getId();
        $permitTypeConfig = $this->typeBasedPermitTypeConfigProvider->getPermitTypeConfig($irhpPermitTypeId);
        $restrictedCountryIds = $permitTypeConfig->getRestrictedCountryIds();

        // get ids of countries included in the range
        $includedCountryIds = array_map(
            function ($country) {
                return $country->getId();
            },
            $irhpPermitRange->getCountrys()->toArray()
        );

        // if a restricted country is not specifically included in the range it is a constrained country
        return array_diff($restrictedCountryIds, $includedCountryIds);
    }
}
