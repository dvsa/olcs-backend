
    /**
     * Set the vi action
     *
     * @param string $viAction
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setViAction($viAction)
    {
        $this->viAction = $viAction;

        return $this;
    }

    /**
     * Get the vi action
     *
     * @return string
     */
    public function getViAction()
    {
        return $this->viAction;
    }
