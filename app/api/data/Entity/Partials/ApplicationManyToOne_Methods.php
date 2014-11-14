
    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
