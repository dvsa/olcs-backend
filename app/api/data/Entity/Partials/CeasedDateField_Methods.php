
    /**
     * Set the ceased date
     *
     * @param \DateTime $ceasedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCeasedDate($ceasedDate)
    {
        $this->ceasedDate = $ceasedDate;

        return $this;
    }

    /**
     * Get the ceased date
     *
     * @return \DateTime
     */
    public function getCeasedDate()
    {
        return $this->ceasedDate;
    }
