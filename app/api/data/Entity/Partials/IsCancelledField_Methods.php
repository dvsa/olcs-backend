
    /**
     * Set the is cancelled
     *
     * @param string $isCancelled
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get the is cancelled
     *
     * @return string
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }
