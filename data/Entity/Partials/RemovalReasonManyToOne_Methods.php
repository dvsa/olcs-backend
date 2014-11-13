
    /**
     * Set the removal reason
     *
     * @param \Olcs\Db\Entity\RefData $removalReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovalReason($removalReason)
    {
        $this->removalReason = $removalReason;

        return $this;
    }

    /**
     * Get the removal reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRemovalReason()
    {
        return $this->removalReason;
    }
