
    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoPsvAuth;
