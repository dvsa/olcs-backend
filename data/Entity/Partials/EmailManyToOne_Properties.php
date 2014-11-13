
    /**
     * Email
     *
     * @var \Olcs\Db\Entity\Email
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Email", fetch="LAZY")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id", nullable=false)
     */
    protected $email;
