
    /**
     * Set the fee
     *
     * @param \Olcs\Db\Entity\Fee $fee
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get the fee
     *
     * @return \Olcs\Db\Entity\Fee
     */
    public function getFee()
    {
        return $this->fee;
    }
