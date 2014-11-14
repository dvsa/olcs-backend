
    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }
