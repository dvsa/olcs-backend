
    /**
     * Set the is maintenance suitable
     *
     * @param string $isMaintenanceSuitable
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsMaintenanceSuitable($isMaintenanceSuitable)
    {
        $this->isMaintenanceSuitable = $isMaintenanceSuitable;

        return $this;
    }

    /**
     * Get the is maintenance suitable
     *
     * @return string
     */
    public function getIsMaintenanceSuitable()
    {
        return $this->isMaintenanceSuitable;
    }
