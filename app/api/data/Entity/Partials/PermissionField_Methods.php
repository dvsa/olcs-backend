
    /**
     * Set the permission
     *
     * @param string $permission
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get the permission
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }
