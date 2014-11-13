
    /**
     * Set the disc no
     *
     * @param string $discNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDiscNo($discNo)
    {
        $this->discNo = $discNo;

        return $this;
    }

    /**
     * Get the disc no
     *
     * @return string
     */
    public function getDiscNo()
    {
        return $this->discNo;
    }
