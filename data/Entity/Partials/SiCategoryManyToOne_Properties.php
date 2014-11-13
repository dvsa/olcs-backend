
    /**
     * Si category
     *
     * @var \Olcs\Db\Entity\SiCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="si_category_id", referencedColumnName="id", nullable=false)
     */
    protected $siCategory;
