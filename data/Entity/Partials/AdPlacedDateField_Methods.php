
    /**
     * Set the ad placed date
     *
     * @param \DateTime $adPlacedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlacedDate($adPlacedDate)
    {
        $this->adPlacedDate = $adPlacedDate;

        return $this;
    }

    /**
     * Get the ad placed date
     *
     * @return \DateTime
     */
    public function getAdPlacedDate()
    {
        return $this->adPlacedDate;
    }
