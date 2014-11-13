
    /**
     * Set the received date
     *
     * @param \DateTime $receivedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get the received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }
