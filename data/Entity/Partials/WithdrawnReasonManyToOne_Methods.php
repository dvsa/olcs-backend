
    /**
     * Set the withdrawn reason
     *
     * @param \Olcs\Db\Entity\RefData $withdrawnReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }
