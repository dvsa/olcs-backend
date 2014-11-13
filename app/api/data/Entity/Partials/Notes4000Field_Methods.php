
    /**
     * Set the notes
     *
     * @param string $notes
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
