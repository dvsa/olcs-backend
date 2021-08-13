<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Organisation\OrganisationBuilder;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class LicenceBuilder implements BuilderInterface
{
    protected const AN_ID = 1;
    protected const ANOTHER_ID = 2;
    protected const ONE_VEHICLE = 1;
    protected const NO_EXTRA_VEHICLES = 0;

    /**
     * @var Licence
     */
    protected $instance;

    /**
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        $this->instance = new Licence(OrganisationBuilder::anOrganisation()->build(), new RefData(Licence::LICENCE_STATUS_VALID));
        $this->instance->setId(null === $id ? static::AN_ID : $id);
    }

    /**
     * @return $this
     */
    public function forGoodsVehicles(): self
    {
        $this->instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        return $this;
    }

    /**
     * @return $this
     */
    public function forPublicServiceVehicles(): self
    {
        $this->instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_PSV));
        return $this;
    }

    /**
     * @return $this
     */
    public function ofTypeRestricted(): self
    {
        $this->instance->setLicenceType(new RefData(Licence::LICENCE_TYPE_RESTRICTED));
        return $this;
    }

    /**
     * @return $this
     */
    public function ofTypeStandardInternational(): self
    {
        $this->instance->setLicenceType(new RefData(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL));
        return $this;
    }

    /**
     * @param int $vehicleCount
     * @return $this
     */
    public function authorizedFor(int $vehicleCount = 0): self
    {
        $this->instance->setTotAuthVehicles($vehicleCount);
        return $this;
    }

    /**
     * @return $this
     */
    public function withNoExtraOperatingCentreCapacity(): self
    {
        return $this->withExtraOperatingCentreCapacityFor(static::NO_EXTRA_VEHICLES);
    }

    /**
     * @param int $extraVehicles
     * @return $this
     */
    public function withExtraOperatingCentreCapacityFor(int $extraVehicles): self
    {
        $operatingCentre1 = LicenceOperatingCentreBuilder::forLicence($this->instance, static::AN_ID)->build();
        $operatingCentre1->setNoOfVehiclesRequired($this->instance->getTotAuthVehicles());

        $operatingCentre2 = LicenceOperatingCentreBuilder::forLicence($this->instance, static::ANOTHER_ID)->build();
        $operatingCentre2->setNoOfVehiclesRequired($extraVehicles);

        $this->instance->setOperatingCentres(new ArrayCollection([$operatingCentre1, $operatingCentre2]));
        return $this;
    }

    /**
     * @param array[] $operatingCentresCapacities Arrays of operating centre capacities in the format [vehicles]
     * @return $this
     */
    public function withOperatingCentresWithCapacitiesFor(array $operatingCentresCapacities): self
    {
        $operatingCentres = [];
        foreach (array_values($operatingCentresCapacities) as $i => $operatingCentreCapacities) {
            $operatingCentres[] = LicenceOperatingCentreBuilder::forLicence($this->instance, $i)
                ->withVehicleCapacity(...$operatingCentreCapacities)
                ->build();
        }
        $this->instance->setOperatingCentres(new ArrayCollection($operatingCentres));
        return $this;
    }

    /**
     * @return $this
     */
    public function withValidVehicleAuthorizations(): self
    {
        $this->withOperatingCentresWithCapacitiesFor([[static::ONE_VEHICLE]]);
        $this->instance->setTotAuthVehicles(static::ONE_VEHICLE);
        return $this;
    }

    /**
     * @return Licence
     */
    public function build(): Licence
    {
        return $this->instance;
    }

    /**
     * @param int|null $id
     * @return static
     */
    public static function aLicence(int $id = null): self
    {
        return new static($id);
    }

    /**
     * @param int|null $id
     * @return static
     */
    public static function aGoodsLicence(int $id = null): self
    {
        return static::aLicence($id)->forGoodsVehicles();
    }

    /**
     * @return static
     */
    public static function aPsvLicence(int $id = null): self
    {
        return static::aLicence($id)->forPublicServiceVehicles();
    }
}
