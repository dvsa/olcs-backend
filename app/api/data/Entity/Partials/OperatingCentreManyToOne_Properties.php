
    /**
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre", fetch="LAZY")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=true)
     */
    protected $operatingCentre;
