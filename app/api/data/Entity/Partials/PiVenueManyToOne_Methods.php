
    /**
     * Set the pi venue
     *
     * @param \Olcs\Db\Entity\PiVenue $piVenue
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiVenue($piVenue)
    {
        $this->piVenue = $piVenue;

        return $this;
    }

    /**
     * Get the pi venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getPiVenue()
    {
        return $this->piVenue;
    }
