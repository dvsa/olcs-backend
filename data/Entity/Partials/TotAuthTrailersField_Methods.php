
    /**
     * Set the tot auth trailers
     *
     * @param int $totAuthTrailers
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTotAuthTrailers($totAuthTrailers)
    {
        $this->totAuthTrailers = $totAuthTrailers;

        return $this;
    }

    /**
     * Get the tot auth trailers
     *
     * @return int
     */
    public function getTotAuthTrailers()
    {
        return $this->totAuthTrailers;
    }
