
    /**
     * Set the irfo psv auth
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuth $irfoPsvAuth
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIrfoPsvAuth($irfoPsvAuth)
    {
        $this->irfoPsvAuth = $irfoPsvAuth;

        return $this;
    }

    /**
     * Get the irfo psv auth
     *
     * @return \Olcs\Db\Entity\IrfoPsvAuth
     */
    public function getIrfoPsvAuth()
    {
        return $this->irfoPsvAuth;
    }
