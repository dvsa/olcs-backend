
    /**
     * Set the cancelled date
     *
     * @param \DateTime $cancelledDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get the cancelled date
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }
