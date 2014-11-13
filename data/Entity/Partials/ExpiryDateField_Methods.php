
    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }
