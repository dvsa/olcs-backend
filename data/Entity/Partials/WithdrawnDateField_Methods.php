
    /**
     * Set the withdrawn date
     *
     * @param \DateTime $withdrawnDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnDate($withdrawnDate)
    {
        $this->withdrawnDate = $withdrawnDate;

        return $this;
    }

    /**
     * Get the withdrawn date
     *
     * @return \DateTime
     */
    public function getWithdrawnDate()
    {
        return $this->withdrawnDate;
    }
