
    /**
     * Set the licence type
     *
     * @param \Olcs\Db\Entity\RefData $licenceType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }
