
    /**
     * Set the document
     *
     * @param \Olcs\Db\Entity\Document $document
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
