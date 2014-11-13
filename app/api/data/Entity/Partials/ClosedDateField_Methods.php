
    /**
     * Set the closed date
     *
     * @param \DateTime $closedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setClosedDate($closedDate)
    {
        $this->closedDate = $closedDate;

        return $this;
    }

    /**
     * Get the closed date
     *
     * @return \DateTime
     */
    public function getClosedDate()
    {
        return $this->closedDate;
    }
