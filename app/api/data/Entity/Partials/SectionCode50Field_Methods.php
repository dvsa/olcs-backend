
    /**
     * Set the section code
     *
     * @param string $sectionCode
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSectionCode($sectionCode)
    {
        $this->sectionCode = $sectionCode;

        return $this;
    }

    /**
     * Get the section code
     *
     * @return string
     */
    public function getSectionCode()
    {
        return $this->sectionCode;
    }
