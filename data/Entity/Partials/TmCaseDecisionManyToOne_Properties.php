
    /**
     * Tm case decision
     *
     * @var \Olcs\Db\Entity\TmCaseDecision
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TmCaseDecision", fetch="LAZY")
     * @ORM\JoinColumn(name="tm_case_decision_id", referencedColumnName="id", nullable=false)
     */
    protected $tmCaseDecision;
