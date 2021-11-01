<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Entity\Traits;

/**
 * @see \Dvsa\OlcsTest\Api\Entity\Traits\TotAuthVehiclesTraitTest
 */
trait TotAuthVehiclesTrait
{
    /**
     * Update the total number of hgv vehicles authorized and refresh the property containing the total of hgv and lgv
     * vehicles authorized.
     *
     * @param int|null $totAuthHgvVehicles
     * @return self
     */
    public function updateTotAuthHgvVehicles(?int $totAuthHgvVehicles): self
    {
        parent::setTotAuthHgvVehicles($totAuthHgvVehicles);
        return $this->updateTotAuthVehicles();
    }

    /**
     * Update the total number of lgv vehicles authorized and refresh the property containing the total of hgv and lgv
     * vehicles authorized.
     *
     * @param int|null $totAuthLgvVehicles
     * @return self
     */
    public function updateTotAuthLgvVehicles(?int $totAuthLgvVehicles): self
    {
        parent::setTotAuthLgvVehicles($totAuthLgvVehicles);
        return $this->updateTotAuthVehicles();
    }

    /**
     * Refresh the property containing the total of hgv and lgv authorized vehicles
     */
    private function updateTotAuthVehicles(): self
    {
        $this->totAuthVehicles = ($this->totAuthHgvVehicles ?? 0) + ($this->totAuthLgvVehicles ?? 0);
        return $this;
    }

    /**
     * Return the total HGV vehicle authorisation if not null, or zero if the value is null
     *
     * @return int
     */
    public function getTotAuthHgvVehiclesZeroCoalesced()
    {
        return $this->getTotAuthHgvVehicles() ?? 0;
    }

    /**
     * Return the total LGV vehicle authorisation if not null, or zero if the value is null
     *
     * @return int
     */
    public function getTotAuthLgvVehiclesZeroCoalesced()
    {
        return $this->getTotAuthLgvVehicles() ?? 0;
    }
}
