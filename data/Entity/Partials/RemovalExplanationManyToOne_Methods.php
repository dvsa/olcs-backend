
    /**
     * Set the removal explanation
     *
     * @param \Olcs\Db\Entity\RefData $removalExplanation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovalExplanation($removalExplanation)
    {
        $this->removalExplanation = $removalExplanation;

        return $this;
    }

    /**
     * Get the removal explanation
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRemovalExplanation()
    {
        return $this->removalExplanation;
    }
