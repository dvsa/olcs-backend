
    /**
     * Set the agreed date
     *
     * @param \DateTime $agreedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAgreedDate($agreedDate)
    {
        $this->agreedDate = $agreedDate;

        return $this;
    }

    /**
     * Get the agreed date
     *
     * @return \DateTime
     */
    public function getAgreedDate()
    {
        return $this->agreedDate;
    }
