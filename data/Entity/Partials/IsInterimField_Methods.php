
    /**
     * Set the is interim
     *
     * @param string $isInterim
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return string
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }
