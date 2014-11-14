
    /**
     * Set the removed date
     *
     * @param \DateTime $removedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovedDate($removedDate)
    {
        $this->removedDate = $removedDate;

        return $this;
    }

    /**
     * Get the removed date
     *
     * @return \DateTime
     */
    public function getRemovedDate()
    {
        return $this->removedDate;
    }
