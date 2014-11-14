
    /**
     * Removal explanation
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="removal_explanation", referencedColumnName="id", nullable=true)
     */
    protected $removalExplanation;
