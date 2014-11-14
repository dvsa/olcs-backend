
    /**
     * Set the pi venue other
     *
     * @param string $piVenueOther
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiVenueOther($piVenueOther)
    {
        $this->piVenueOther = $piVenueOther;

        return $this;
    }

    /**
     * Get the pi venue other
     *
     * @return string
     */
    public function getPiVenueOther()
    {
        return $this->piVenueOther;
    }
