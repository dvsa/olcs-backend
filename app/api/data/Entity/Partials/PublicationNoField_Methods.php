
    /**
     * Set the publication no
     *
     * @param int $publicationNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;

        return $this;
    }

    /**
     * Get the publication no
     *
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }
