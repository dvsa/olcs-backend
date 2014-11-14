
    /**
     * Set the hearing date
     *
     * @param \DateTime $hearingDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHearingDate($hearingDate)
    {
        $this->hearingDate = $hearingDate;

        return $this;
    }

    /**
     * Get the hearing date
     *
     * @return \DateTime
     */
    public function getHearingDate()
    {
        return $this->hearingDate;
    }
