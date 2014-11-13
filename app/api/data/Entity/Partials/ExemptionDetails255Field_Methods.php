
    /**
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setExemptionDetails($exemptionDetails)
    {
        $this->exemptionDetails = $exemptionDetails;

        return $this;
    }

    /**
     * Get the exemption details
     *
     * @return string
     */
    public function getExemptionDetails()
    {
        return $this->exemptionDetails;
    }
