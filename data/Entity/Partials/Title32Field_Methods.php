
    /**
     * Set the title
     *
     * @param string $title
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
