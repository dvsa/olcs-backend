
    /**
     * Set the effective date
     *
     * @param \DateTime $effectiveDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get the effective date
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }
