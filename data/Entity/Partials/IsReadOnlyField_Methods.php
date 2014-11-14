
    /**
     * Set the is read only
     *
     * @param boolean $isReadOnly
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return boolean
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }
