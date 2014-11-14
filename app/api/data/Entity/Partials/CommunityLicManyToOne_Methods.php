
    /**
     * Set the community lic
     *
     * @param \Olcs\Db\Entity\CommunityLic $communityLic
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCommunityLic($communityLic)
    {
        $this->communityLic = $communityLic;

        return $this;
    }

    /**
     * Get the community lic
     *
     * @return \Olcs\Db\Entity\CommunityLic
     */
    public function getCommunityLic()
    {
        return $this->communityLic;
    }
