
    /**
     * Set the user
     *
     * @param \Olcs\Db\Entity\User $user
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
