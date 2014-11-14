
    /**
     * Set the tot community licences
     *
     * @param int $totCommunityLicences
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotCommunityLicences($totCommunityLicences)
    {
        $this->totCommunityLicences = $totCommunityLicences;

        return $this;
    }

    /**
     * Get the tot community licences
     *
     * @return int
     */
    public function getTotCommunityLicences()
    {
        return $this->totCommunityLicences;
    }
