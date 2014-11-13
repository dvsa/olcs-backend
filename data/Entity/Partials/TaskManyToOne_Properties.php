
    /**
     * Task
     *
     * @var \Olcs\Db\Entity\Task
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Task", fetch="LAZY")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    protected $task;
