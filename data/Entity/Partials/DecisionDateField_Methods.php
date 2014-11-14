
    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get the decision date
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }
