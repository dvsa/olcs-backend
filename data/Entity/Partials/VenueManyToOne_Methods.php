
    /**
     * Set the venue
     *
     * @param \Olcs\Db\Entity\PiVenue $venue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get the venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getVenue()
    {
        return $this->venue;
    }
