
    /**
     * Set the si category
     *
     * @param \Olcs\Db\Entity\SiCategory $siCategory
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSiCategory($siCategory)
    {
        $this->siCategory = $siCategory;

        return $this;
    }

    /**
     * Get the si category
     *
     * @return \Olcs\Db\Entity\SiCategory
     */
    public function getSiCategory()
    {
        return $this->siCategory;
    }
