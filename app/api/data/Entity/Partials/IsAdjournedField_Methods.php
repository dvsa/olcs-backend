
    /**
     * Set the is adjourned
     *
     * @param string $isAdjourned
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return string
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
    }
