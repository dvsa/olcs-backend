
    /**
     * Set the tm case decision
     *
     * @param \Olcs\Db\Entity\TmCaseDecision $tmCaseDecision
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTmCaseDecision($tmCaseDecision)
    {
        $this->tmCaseDecision = $tmCaseDecision;

        return $this;
    }

    /**
     * Get the tm case decision
     *
     * @return \Olcs\Db\Entity\TmCaseDecision
     */
    public function getTmCaseDecision()
    {
        return $this->tmCaseDecision;
    }
