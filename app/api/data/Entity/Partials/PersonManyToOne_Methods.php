
    /**
     * Set the person
     *
     * @param \Olcs\Db\Entity\Person $person
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get the person
     *
     * @return \Olcs\Db\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
