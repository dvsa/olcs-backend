<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\OlcsTest\Builder\BuilderInterface;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

class LicenceOperatingCentreBuilder implements BuilderInterface
{
    protected const AN_ID = 1;

    /**
     * @var LicenceOperatingCentre
     */
    protected $instance;

    /**
     * @param Licence $licence
     * @param int|null $id
     */
    public function __construct(Licence $licence, ?int $id = self::AN_ID)
    {
        $oc = new OperatingCentre();
        $oc->setId($id);
        $this->instance = new LicenceOperatingCentre($licence, $oc);
        $this->instance->setId($id);
    }

    /**
     * @param int $hgvCapacity
     * @param int $lgvCapacity
     * @return self
     */
    public function withVehicleCapacities(int $hgvCapacity, int $lgvCapacity): self
    {
        $this->instance->updateNoOfHgvVehiclesRequired($hgvCapacity);
        $this->instance->updateNoOfLgvVehiclesRequired($lgvCapacity);
        return $this;
    }

    /**
     * @param int $hgvCount
     * @return self
     */
    public function requiringOnlyHgvs(int $hgvCount): self
    {
        $this->instance->updateNoOfHgvVehiclesRequired($hgvCount);
        $this->instance->updateNoOfLgvVehiclesRequired(0);
        return $this;
    }

    /**
     * @return LicenceOperatingCentre
     */
    public function build(): LicenceOperatingCentre
    {
        return $this->instance;
    }

    /**
     * @param Licence $licence
     * @param int|null $id
     * @return self
     */
    public static function forLicence(Licence $licence, int $id = null): self
    {
        return new static($licence, $id);
    }
}
