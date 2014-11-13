
    /**
     * Enforcement area
     *
     * @var \Olcs\Db\Entity\EnforcementArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EnforcementArea", fetch="LAZY")
     * @ORM\JoinColumn(name="enforcement_area_id", referencedColumnName="id", nullable=false)
     */
    protected $enforcementArea;
