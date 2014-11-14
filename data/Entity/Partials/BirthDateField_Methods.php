
    /**
     * Set the birth date
     *
     * @param \DateTime $birthDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get the birth date
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }
