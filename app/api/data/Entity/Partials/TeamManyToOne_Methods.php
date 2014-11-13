
    /**
     * Set the team
     *
     * @param \Olcs\Db\Entity\Team $team
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Olcs\Db\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
