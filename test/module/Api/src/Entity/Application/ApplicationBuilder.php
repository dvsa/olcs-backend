<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Dvsa\OlcsTest\Api\Entity\Licence\LicenceBuilder;
use Doctrine\Common\Collections\ArrayCollection;

class ApplicationBuilder implements BuilderInterface
{
    protected const AN_ID = 1;
    protected const ANOTHER_ID = 2;
    protected const NO_EXTRA_HGVS = 0;
    protected const NO_EXTRA_LGVS = 0;

    /**
     * @var Application
     */
    protected $instance;

    /**
     * @param int|null $id
     */
    public function __construct(Licence $licence, int $id = null, bool $isVariation = false)
    {
        $this->instance = new Application($licence, new RefData(Application::APPLICATION_STATUS_NOT_SUBMITTED), $isVariation);
        $this->instance->setId(null === $id ? self::AN_ID : $id);
        $this->instance->copyInformationFromLicence($licence);
    }

    /**
     * @return $this
     */
    public function withCompletionShowingUpdatedOperatingCentres(): self
    {
        $this->instance->setApplicationCompletion(
            ApplicationCompletionBuilder::forApplication($this->instance)->withUpdatedOperatingCentresSection()->build()
        );
        return $this;
    }

    /**
     * @return $this
     */
    public function withApplicationCompletion(): self
    {
        $this->instance->setApplicationCompletion(ApplicationCompletionBuilder::forApplication($this->instance)->build());
        return $this;
    }

    /**
     * @param int $hgvCount
     * @param int $lgvCount
     * @return $this
     */
    public function authorizedFor(int $hgvCount = 0, int $lgvCount = 0): self
    {
        $this->instance->updateTotAuthHgvVehicles($hgvCount);
        $this->instance->updateTotAuthLgvVehicles($lgvCount);
        return $this;
    }

    /**
     * @return $this
     */
    public function withNoExtraOperatingCentreCapacity(): self
    {
        $operatingCentre = ApplicationOperatingCentreBuilder::forApplication($this->instance, static::AN_ID)->build();
        $operatingCentre->updateNoOfHgvVehiclesRequired($this->instance->getTotAuthHgvVehicles());
        $operatingCentre->updateNoOfLgvVehiclesRequired($this->instance->getTotAuthLgvVehicles());
        $this->instance->setOperatingCentres(new ArrayCollection([$operatingCentre]));
        return $this;
    }

    /**
     * @param int $extraHgvs
     * @param int $extraLgvs
     * @return $this
     */
    public function withExtraOperatingCentreCapacityFor(int $extraHgvs, int $extraLgvs = 0): self
    {
        $operatingCentre1 = ApplicationOperatingCentreBuilder::forApplication($this->instance, static::AN_ID)->build();
        $operatingCentre1->updateNoOfHgvVehiclesRequired($this->instance->getTotAuthHgvVehicles());
        $operatingCentre1->updateNoOfLgvVehiclesRequired($this->instance->getTotAuthLgvVehicles());

        $operatingCentre2 = ApplicationOperatingCentreBuilder::forApplication($this->instance, static::ANOTHER_ID)->build();
        $operatingCentre2->updateNoOfHgvVehiclesRequired($extraHgvs);
        $operatingCentre2->updateNoOfLgvVehiclesRequired($extraLgvs);

        $this->instance->setOperatingCentres(new ArrayCollection([$operatingCentre1, $operatingCentre2]));
        return $this;
    }

    /**
     * @param array[] $operatingCentresCapacities Arrays of operating centre capacities in the format [hgvs, lgvs]
     * @return $this
     */
    public function withOperatingCentresWithCapacitiesFor(array $operatingCentresCapacities): self
    {
        $operatingCentres = [];
        foreach (array_values($operatingCentresCapacities) as $i => $operatingCentreCapacities) {
            $operatingCentres[] = ApplicationOperatingCentreBuilder::forApplication($this->instance, $i)
                ->withVehicleCapacities($operatingCentreCapacities[0], $operatingCentreCapacities[1])
                ->build();
        }
        $this->instance->setOperatingCentres(new ArrayCollection($operatingCentres));
        return $this;
    }


    /**
     * @return Application
     */
    public function build(): Application
    {
        return $this->instance;
    }

    /**
     * @param Licence|LicenceBuilder $licence
     * @param int|null $id
     * @return self
     */
    public static function applicationForLicence($licence, int $id = null): self
    {
        if ($licence instanceof LicenceBuilder) {
            $licence = $licence->build();
        }
        return new static($licence, $id, false);
    }

    /**
     * @param int|null $id
     * @return self
     */
    public static function application(int $id = null): self
    {
        return static::applicationForLicence(LicenceBuilder::aLicence($id));
    }

    /**
     * @param Licence|LicenceBuilder $licence
     * @param int|null $id
     * @return self
     */
    public static function variationForLicence($licence, int $id = null): self
    {
        if ($licence instanceof LicenceBuilder) {
            $licence = $licence->build();
        }
        return new static($licence, $id, true);
    }

    /**
     * @param int|null $id
     * @return self
     */
    public static function variation(int $id = null): self
    {
        return static::variationForLicence(LicenceBuilder::aLicence($id));
    }
}
