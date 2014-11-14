
    /**
     * Set the presiding tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $presidingTc
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPresidingTc($presidingTc)
    {
        $this->presidingTc = $presidingTc;

        return $this;
    }

    /**
     * Get the presiding tc
     *
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getPresidingTc()
    {
        return $this->presidingTc;
    }
