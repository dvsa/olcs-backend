
    /**
     * Set the penalty
     *
     * @param string $penalty
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;

        return $this;
    }

    /**
     * Get the penalty
     *
     * @return string
     */
    public function getPenalty()
    {
        return $this->penalty;
    }
