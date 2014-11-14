
    /**
     * Set the ad placed
     *
     * @param string $adPlaced
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlaced($adPlaced)
    {
        $this->adPlaced = $adPlaced;

        return $this;
    }

    /**
     * Get the ad placed
     *
     * @return string
     */
    public function getAdPlaced()
    {
        return $this->adPlaced;
    }
