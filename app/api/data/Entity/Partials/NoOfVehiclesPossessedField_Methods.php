
    /**
     * Set the no of vehicles possessed
     *
     * @param int $noOfVehiclesPossessed
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfVehiclesPossessed($noOfVehiclesPossessed)
    {
        $this->noOfVehiclesPossessed = $noOfVehiclesPossessed;

        return $this;
    }

    /**
     * Get the no of vehicles possessed
     *
     * @return int
     */
    public function getNoOfVehiclesPossessed()
    {
        return $this->noOfVehiclesPossessed;
    }
