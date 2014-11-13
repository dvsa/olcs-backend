
    /**
     * Set the irfo fee id
     *
     * @param string $irfoFeeId
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoFeeId($irfoFeeId)
    {
        $this->irfoFeeId = $irfoFeeId;

        return $this;
    }

    /**
     * Get the irfo fee id
     *
     * @return string
     */
    public function getIrfoFeeId()
    {
        return $this->irfoFeeId;
    }
