
    /**
     * Set the no of vehicles required
     *
     * @param int $noOfVehiclesRequired
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfVehiclesRequired($noOfVehiclesRequired)
    {
        $this->noOfVehiclesRequired = $noOfVehiclesRequired;

        return $this;
    }

    /**
     * Get the no of vehicles required
     *
     * @return int
     */
    public function getNoOfVehiclesRequired()
    {
        return $this->noOfVehiclesRequired;
    }
