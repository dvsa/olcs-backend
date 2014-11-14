
    /**
     * Set the in force date
     *
     * @param \DateTime $inForceDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setInForceDate($inForceDate)
    {
        $this->inForceDate = $inForceDate;

        return $this;
    }

    /**
     * Get the in force date
     *
     * @return \DateTime
     */
    public function getInForceDate()
    {
        return $this->inForceDate;
    }
