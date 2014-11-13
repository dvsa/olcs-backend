
    /**
     * Set the local authority
     *
     * @param \Olcs\Db\Entity\LocalAuthority $localAuthority
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLocalAuthority($localAuthority)
    {
        $this->localAuthority = $localAuthority;

        return $this;
    }

    /**
     * Get the local authority
     *
     * @return \Olcs\Db\Entity\LocalAuthority
     */
    public function getLocalAuthority()
    {
        return $this->localAuthority;
    }
