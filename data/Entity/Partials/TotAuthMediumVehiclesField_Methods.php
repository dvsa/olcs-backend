
    /**
     * Set the tot auth medium vehicles
     *
     * @param int $totAuthMediumVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthMediumVehicles($totAuthMediumVehicles)
    {
        $this->totAuthMediumVehicles = $totAuthMediumVehicles;

        return $this;
    }

    /**
     * Get the tot auth medium vehicles
     *
     * @return int
     */
    public function getTotAuthMediumVehicles()
    {
        return $this->totAuthMediumVehicles;
    }
