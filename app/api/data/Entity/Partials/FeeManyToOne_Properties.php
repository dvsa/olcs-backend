
    /**
     * Fee
     *
     * @var \Olcs\Db\Entity\Fee
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Fee", fetch="LAZY")
     * @ORM\JoinColumn(name="fee_id", referencedColumnName="id", nullable=false)
     */
    protected $fee;
