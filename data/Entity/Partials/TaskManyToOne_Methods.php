
    /**
     * Set the task
     *
     * @param \Olcs\Db\Entity\Task $task
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get the task
     *
     * @return \Olcs\Db\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }
