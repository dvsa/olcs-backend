
    /**
     * Set the is deleted
     *
     * @param string $isDeleted
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get the is deleted
     *
     * @return string
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
