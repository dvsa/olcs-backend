
    /**
     * Set the pub type
     *
     * @param string $pubType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPubType($pubType)
    {
        $this->pubType = $pubType;

        return $this;
    }

    /**
     * Get the pub type
     *
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
    }
