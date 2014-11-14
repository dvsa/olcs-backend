
    /**
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;
