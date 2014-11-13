
    /**
     * Removal reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="removal_reason", referencedColumnName="id", nullable=true)
     */
    protected $removalReason;
