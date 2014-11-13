
    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }
