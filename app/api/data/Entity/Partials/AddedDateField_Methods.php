
    /**
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }
