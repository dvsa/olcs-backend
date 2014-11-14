
    /**
     * Set the enforcement area
     *
     * @param \Olcs\Db\Entity\EnforcementArea $enforcementArea
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEnforcementArea($enforcementArea)
    {
        $this->enforcementArea = $enforcementArea;

        return $this;
    }

    /**
     * Get the enforcement area
     *
     * @return \Olcs\Db\Entity\EnforcementArea
     */
    public function getEnforcementArea()
    {
        return $this->enforcementArea;
    }
