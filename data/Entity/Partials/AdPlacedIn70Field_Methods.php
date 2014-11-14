
    /**
     * Set the ad placed in
     *
     * @param string $adPlacedIn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAdPlacedIn($adPlacedIn)
    {
        $this->adPlacedIn = $adPlacedIn;

        return $this;
    }

    /**
     * Get the ad placed in
     *
     * @return string
     */
    public function getAdPlacedIn()
    {
        return $this->adPlacedIn;
    }
