
    /**
     * Category
     *
     * @var \Olcs\Db\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Category", fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;
