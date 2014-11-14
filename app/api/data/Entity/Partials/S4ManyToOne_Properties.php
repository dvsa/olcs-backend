
    /**
     * S4
     *
     * @var \Olcs\Db\Entity\S4
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\S4", fetch="LAZY")
     * @ORM\JoinColumn(name="s4_id", referencedColumnName="id", nullable=true)
     */
    protected $s4;
