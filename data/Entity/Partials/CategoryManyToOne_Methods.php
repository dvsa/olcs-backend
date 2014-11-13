
    /**
     * Set the category
     *
     * @param \Olcs\Db\Entity\Category $category
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the category
     *
     * @return \Olcs\Db\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
