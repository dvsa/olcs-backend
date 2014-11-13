
    /**
     * Set the tot auth small vehicles
     *
     * @param int $totAuthSmallVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthSmallVehicles($totAuthSmallVehicles)
    {
        $this->totAuthSmallVehicles = $totAuthSmallVehicles;

        return $this;
    }

    /**
     * Get the tot auth small vehicles
     *
     * @return int
     */
    public function getTotAuthSmallVehicles()
    {
        return $this->totAuthSmallVehicles;
    }
