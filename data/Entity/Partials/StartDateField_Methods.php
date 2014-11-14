
    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
