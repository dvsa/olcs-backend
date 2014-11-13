
    /**
     * Community lic
     *
     * @var \Olcs\Db\Entity\CommunityLic
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLic", fetch="LAZY")
     * @ORM\JoinColumn(name="community_lic_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLic;
