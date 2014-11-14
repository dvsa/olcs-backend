
    /**
     * Set the outcome
     *
     * @param \Olcs\Db\Entity\RefData $outcome
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOutcome()
    {
        return $this->outcome;
    }
