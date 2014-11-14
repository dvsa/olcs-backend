
    /**
     * Set the adjourned date
     *
     * @param \DateTime $adjournedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdjournedDate($adjournedDate)
    {
        $this->adjournedDate = $adjournedDate;

        return $this;
    }

    /**
     * Get the adjourned date
     *
     * @return \DateTime
     */
    public function getAdjournedDate()
    {
        return $this->adjournedDate;
    }
