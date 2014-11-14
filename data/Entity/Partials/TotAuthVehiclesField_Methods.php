
    /**
     * Set the tot auth vehicles
     *
     * @param int $totAuthVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthVehicles($totAuthVehicles)
    {
        $this->totAuthVehicles = $totAuthVehicles;

        return $this;
    }

    /**
     * Get the tot auth vehicles
     *
     * @return int
     */
    public function getTotAuthVehicles()
    {
        return $this->totAuthVehicles;
    }
