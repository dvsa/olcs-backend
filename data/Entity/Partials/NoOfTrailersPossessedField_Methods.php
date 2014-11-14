
    /**
     * Set the no of trailers possessed
     *
     * @param int $noOfTrailersPossessed
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfTrailersPossessed($noOfTrailersPossessed)
    {
        $this->noOfTrailersPossessed = $noOfTrailersPossessed;

        return $this;
    }

    /**
     * Get the no of trailers possessed
     *
     * @return int
     */
    public function getNoOfTrailersPossessed()
    {
        return $this->noOfTrailersPossessed;
    }
