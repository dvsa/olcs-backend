
    /**
     * Set the witnesses
     *
     * @param int $witnesses
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWitnesses($witnesses)
    {
        $this->witnesses = $witnesses;

        return $this;
    }

    /**
     * Get the witnesses
     *
     * @return int
     */
    public function getWitnesses()
    {
        return $this->witnesses;
    }
