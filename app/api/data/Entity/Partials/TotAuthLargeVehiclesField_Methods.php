
    /**
     * Set the tot auth large vehicles
     *
     * @param int $totAuthLargeVehicles
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthLargeVehicles($totAuthLargeVehicles)
    {
        $this->totAuthLargeVehicles = $totAuthLargeVehicles;

        return $this;
    }

    /**
     * Get the tot auth large vehicles
     *
     * @return int
     */
    public function getTotAuthLargeVehicles()
    {
        return $this->totAuthLargeVehicles;
    }
