
    /**
     * Set the irfo gv permit
     *
     * @param \Olcs\Db\Entity\IrfoGvPermit $irfoGvPermit
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoGvPermit($irfoGvPermit)
    {
        $this->irfoGvPermit = $irfoGvPermit;

        return $this;
    }

    /**
     * Get the irfo gv permit
     *
     * @return \Olcs\Db\Entity\IrfoGvPermit
     */
    public function getIrfoGvPermit()
    {
        return $this->irfoGvPermit;
    }
