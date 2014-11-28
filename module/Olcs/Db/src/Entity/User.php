<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="user",
 *    indexes={
 *        @ORM\Index(name="fk_user_team1_idx", columns={"team_id"}),
 *        @ORM\Index(name="fk_user_local_authority1_idx", columns={"local_authority_id"}),
 *        @ORM\Index(name="fk_user_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_user_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_user_contact_details1_idx", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_user_contact_details2_idx", columns={"partner_contact_details_id"}),
 *        @ORM\Index(name="fk_user_hint_questions1_idx", columns={"hint_questions_id1"}),
 *        @ORM\Index(name="fk_user_hint_questions2_idx", columns={"hint_questions_id2"}),
 *        @ORM\Index(name="fk_user_transport_manager1_idx", columns={"transport_manager_id"})
 *    }
 * )
 */
class User implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TransportManagerManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\TeamManyToOne,
        Traits\LocalAuthorityManyToOne,
        Traits\EmailAddress45Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Partner contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY")
     * @ORM\JoinColumn(name="partner_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $partnerContactDetails;

    /**
     * Hint questions1
     *
     * @var \Olcs\Db\Entity\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\HintQuestion", fetch="LAZY")
     * @ORM\JoinColumn(name="hint_questions_id1", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestions1;

    /**
     * Hint questions2
     *
     * @var \Olcs\Db\Entity\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\HintQuestion", fetch="LAZY")
     * @ORM\JoinColumn(name="hint_questions_id2", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestions2;

    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY")
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $contactDetails;

    /**
     * Pid
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="pid", nullable=true)
     */
    protected $pid;

    /**
     * Login id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="login_id", length=40, nullable=true)
     */
    protected $loginId;

    /**
     * Account disabled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="account_disabled", nullable=false)
     */
    protected $accountDisabled = 0;

    /**
     * Locked date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="locked_date", nullable=true)
     */
    protected $lockedDate;

    /**
     * Attempts
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="attempts", nullable=true)
     */
    protected $attempts;

    /**
     * Last successful login date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_successful_login_date", nullable=true)
     */
    protected $lastSuccessfulLoginDate;

    /**
     * Hint answer1
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hint_answer_1", length=50, nullable=true)
     */
    protected $hintAnswer1;

    /**
     * Hint answer2
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hint_answer_2", length=50, nullable=true)
     */
    protected $hintAnswer2;

    /**
     * Memorable word
     *
     * @var string
     *
     * @ORM\Column(type="string", name="memorable_word", length=10, nullable=true)
     */
    protected $memorableWord;

    /**
     * Memorable word digit1
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="memorable_word_digit1", nullable=true)
     */
    protected $memorableWordDigit1;

    /**
     * Memorable word digit2
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="memorable_word_digit2", nullable=true)
     */
    protected $memorableWordDigit2;

    /**
     * Must reset password
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="must_reset_password", nullable=false)
     */
    protected $mustResetPassword = 0;

    /**
     * Password expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="password_expiry_date", nullable=true)
     */
    protected $passwordExpiryDate;

    /**
     * Reset password expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="reset_password_expiry_date", nullable=true)
     */
    protected $resetPasswordExpiryDate;

    /**
     * Password reminder sent
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="password_reminder_sent", nullable=true)
     */
    protected $passwordReminderSent;

    /**
     * Locked datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="locked_datetime", nullable=true)
     */
    protected $lockedDatetime;

    /**
     * Job title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="job_title", length=100, nullable=true)
     */
    protected $jobTitle;

    /**
     * Division group
     *
     * @var string
     *
     * @ORM\Column(type="string", name="division_group", length=100, nullable=true)
     */
    protected $divisionGroup;

    /**
     * Department name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="department_name", length=100, nullable=true)
     */
    protected $departmentName;

    /**
     * Organisation user
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationUser", mappedBy="user")
     */
    protected $organisationUsers;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->organisationUsers = new ArrayCollection();
    }

    /**
     * Set the partner contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $partnerContactDetails
     * @return User
     */
    public function setPartnerContactDetails($partnerContactDetails)
    {
        $this->partnerContactDetails = $partnerContactDetails;

        return $this;
    }

    /**
     * Get the partner contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getPartnerContactDetails()
    {
        return $this->partnerContactDetails;
    }

    /**
     * Set the hint questions1
     *
     * @param \Olcs\Db\Entity\HintQuestion $hintQuestions1
     * @return User
     */
    public function setHintQuestions1($hintQuestions1)
    {
        $this->hintQuestions1 = $hintQuestions1;

        return $this;
    }

    /**
     * Get the hint questions1
     *
     * @return \Olcs\Db\Entity\HintQuestion
     */
    public function getHintQuestions1()
    {
        return $this->hintQuestions1;
    }

    /**
     * Set the hint questions2
     *
     * @param \Olcs\Db\Entity\HintQuestion $hintQuestions2
     * @return User
     */
    public function setHintQuestions2($hintQuestions2)
    {
        $this->hintQuestions2 = $hintQuestions2;

        return $this;
    }

    /**
     * Get the hint questions2
     *
     * @return \Olcs\Db\Entity\HintQuestion
     */
    public function getHintQuestions2()
    {
        return $this->hintQuestions2;
    }

    /**
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
     * @return User
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Set the pid
     *
     * @param int $pid
     * @return User
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get the pid
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the login id
     *
     * @param string $loginId
     * @return User
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;

        return $this;
    }

    /**
     * Get the login id
     *
     * @return string
     */
    public function getLoginId()
    {
        return $this->loginId;
    }

    /**
     * Set the account disabled
     *
     * @param string $accountDisabled
     * @return User
     */
    public function setAccountDisabled($accountDisabled)
    {
        $this->accountDisabled = $accountDisabled;

        return $this;
    }

    /**
     * Get the account disabled
     *
     * @return string
     */
    public function getAccountDisabled()
    {
        return $this->accountDisabled;
    }

    /**
     * Set the locked date
     *
     * @param \DateTime $lockedDate
     * @return User
     */
    public function setLockedDate($lockedDate)
    {
        $this->lockedDate = $lockedDate;

        return $this;
    }

    /**
     * Get the locked date
     *
     * @return \DateTime
     */
    public function getLockedDate()
    {
        return $this->lockedDate;
    }

    /**
     * Set the attempts
     *
     * @param int $attempts
     * @return User
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get the attempts
     *
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Set the last successful login date
     *
     * @param \DateTime $lastSuccessfulLoginDate
     * @return User
     */
    public function setLastSuccessfulLoginDate($lastSuccessfulLoginDate)
    {
        $this->lastSuccessfulLoginDate = $lastSuccessfulLoginDate;

        return $this;
    }

    /**
     * Get the last successful login date
     *
     * @return \DateTime
     */
    public function getLastSuccessfulLoginDate()
    {
        return $this->lastSuccessfulLoginDate;
    }

    /**
     * Set the hint answer1
     *
     * @param string $hintAnswer1
     * @return User
     */
    public function setHintAnswer1($hintAnswer1)
    {
        $this->hintAnswer1 = $hintAnswer1;

        return $this;
    }

    /**
     * Get the hint answer1
     *
     * @return string
     */
    public function getHintAnswer1()
    {
        return $this->hintAnswer1;
    }

    /**
     * Set the hint answer2
     *
     * @param string $hintAnswer2
     * @return User
     */
    public function setHintAnswer2($hintAnswer2)
    {
        $this->hintAnswer2 = $hintAnswer2;

        return $this;
    }

    /**
     * Get the hint answer2
     *
     * @return string
     */
    public function getHintAnswer2()
    {
        return $this->hintAnswer2;
    }

    /**
     * Set the memorable word
     *
     * @param string $memorableWord
     * @return User
     */
    public function setMemorableWord($memorableWord)
    {
        $this->memorableWord = $memorableWord;

        return $this;
    }

    /**
     * Get the memorable word
     *
     * @return string
     */
    public function getMemorableWord()
    {
        return $this->memorableWord;
    }

    /**
     * Set the memorable word digit1
     *
     * @param int $memorableWordDigit1
     * @return User
     */
    public function setMemorableWordDigit1($memorableWordDigit1)
    {
        $this->memorableWordDigit1 = $memorableWordDigit1;

        return $this;
    }

    /**
     * Get the memorable word digit1
     *
     * @return int
     */
    public function getMemorableWordDigit1()
    {
        return $this->memorableWordDigit1;
    }

    /**
     * Set the memorable word digit2
     *
     * @param int $memorableWordDigit2
     * @return User
     */
    public function setMemorableWordDigit2($memorableWordDigit2)
    {
        $this->memorableWordDigit2 = $memorableWordDigit2;

        return $this;
    }

    /**
     * Get the memorable word digit2
     *
     * @return int
     */
    public function getMemorableWordDigit2()
    {
        return $this->memorableWordDigit2;
    }

    /**
     * Set the must reset password
     *
     * @param boolean $mustResetPassword
     * @return User
     */
    public function setMustResetPassword($mustResetPassword)
    {
        $this->mustResetPassword = $mustResetPassword;

        return $this;
    }

    /**
     * Get the must reset password
     *
     * @return boolean
     */
    public function getMustResetPassword()
    {
        return $this->mustResetPassword;
    }

    /**
     * Set the password expiry date
     *
     * @param \DateTime $passwordExpiryDate
     * @return User
     */
    public function setPasswordExpiryDate($passwordExpiryDate)
    {
        $this->passwordExpiryDate = $passwordExpiryDate;

        return $this;
    }

    /**
     * Get the password expiry date
     *
     * @return \DateTime
     */
    public function getPasswordExpiryDate()
    {
        return $this->passwordExpiryDate;
    }

    /**
     * Set the reset password expiry date
     *
     * @param \DateTime $resetPasswordExpiryDate
     * @return User
     */
    public function setResetPasswordExpiryDate($resetPasswordExpiryDate)
    {
        $this->resetPasswordExpiryDate = $resetPasswordExpiryDate;

        return $this;
    }

    /**
     * Get the reset password expiry date
     *
     * @return \DateTime
     */
    public function getResetPasswordExpiryDate()
    {
        return $this->resetPasswordExpiryDate;
    }

    /**
     * Set the password reminder sent
     *
     * @param boolean $passwordReminderSent
     * @return User
     */
    public function setPasswordReminderSent($passwordReminderSent)
    {
        $this->passwordReminderSent = $passwordReminderSent;

        return $this;
    }

    /**
     * Get the password reminder sent
     *
     * @return boolean
     */
    public function getPasswordReminderSent()
    {
        return $this->passwordReminderSent;
    }

    /**
     * Set the locked datetime
     *
     * @param \DateTime $lockedDatetime
     * @return User
     */
    public function setLockedDatetime($lockedDatetime)
    {
        $this->lockedDatetime = $lockedDatetime;

        return $this;
    }

    /**
     * Get the locked datetime
     *
     * @return \DateTime
     */
    public function getLockedDatetime()
    {
        return $this->lockedDatetime;
    }

    /**
     * Set the job title
     *
     * @param string $jobTitle
     * @return User
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Get the job title
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set the division group
     *
     * @param string $divisionGroup
     * @return User
     */
    public function setDivisionGroup($divisionGroup)
    {
        $this->divisionGroup = $divisionGroup;

        return $this;
    }

    /**
     * Get the division group
     *
     * @return string
     */
    public function getDivisionGroup()
    {
        return $this->divisionGroup;
    }

    /**
     * Set the department name
     *
     * @param string $departmentName
     * @return User
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get the department name
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set the organisation user
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return User
     */
    public function setOrganisationUsers($organisationUsers)
    {
        $this->organisationUsers = $organisationUsers;

        return $this;
    }

    /**
     * Get the organisation users
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrganisationUsers()
    {
        return $this->organisationUsers;
    }

    /**
     * Add a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return User
     */
    public function addOrganisationUsers($organisationUsers)
    {
        if ($organisationUsers instanceof ArrayCollection) {
            $this->organisationUsers = new ArrayCollection(
                array_merge(
                    $this->organisationUsers->toArray(),
                    $organisationUsers->toArray()
                )
            );
        } elseif (!$this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->add($organisationUsers);
        }

        return $this;
    }

    /**
     * Remove a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return User
     */
    public function removeOrganisationUsers($organisationUsers)
    {
        if ($this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->removeElement($organisationUsers);
        }

        return $this;
    }
}
