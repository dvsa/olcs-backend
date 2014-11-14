
    /**
     * Set the no of trailers required
     *
     * @param int $noOfTrailersRequired
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfTrailersRequired($noOfTrailersRequired)
    {
        $this->noOfTrailersRequired = $noOfTrailersRequired;

        return $this;
    }

    /**
     * Get the no of trailers required
     *
     * @return int
     */
    public function getNoOfTrailersRequired()
    {
        return $this->noOfTrailersRequired;
    }
