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
 *        @ORM\Index(name="ix_user_team_id", columns={"team_id"}),
 *        @ORM\Index(name="ix_user_local_authority_id", columns={"local_authority_id"}),
 *        @ORM\Index(name="ix_user_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_user_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_user_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_user_partner_contact_details_id", columns={"partner_contact_details_id"}),
 *        @ORM\Index(name="ix_user_hint_question_id1", columns={"hint_question_id1"}),
 *        @ORM\Index(name="ix_user_hint_question_id2", columns={"hint_question_id2"}),
 *        @ORM\Index(name="ix_user_transport_manager_id", columns={"transport_manager_id"})
 *    }
 * )
 */
class User implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EmailAddress45Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LocalAuthorityManyToOne,
        Traits\TeamManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;

    /**
     * Account disabled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="account_disabled", nullable=false, options={"default": 0})
     */
    protected $accountDisabled = 0;

    /**
     * Attempts
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="attempts", nullable=true)
     */
    protected $attempts;

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
     * Hint question1
     *
     * @var \Olcs\Db\Entity\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\HintQuestion")
     * @ORM\JoinColumn(name="hint_question_id1", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestion1;

    /**
     * Hint question2
     *
     * @var \Olcs\Db\Entity\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\HintQuestion")
     * @ORM\JoinColumn(name="hint_question_id2", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestion2;

    /**
     * Last successful login date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_successful_login_date", nullable=true)
     */
    protected $lastSuccessfulLoginDate;

    /**
     * Locked date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="locked_date", nullable=true)
     */
    protected $lockedDate;

    /**
     * Login id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="login_id", length=40, nullable=true)
     */
    protected $loginId;

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
     * @ORM\Column(type="smallint", name="memorable_word_digit1", nullable=true)
     */
    protected $memorableWordDigit1;

    /**
     * Memorable word digit2
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="memorable_word_digit2", nullable=true)
     */
    protected $memorableWordDigit2;

    /**
     * Must reset password
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="must_reset_password", nullable=false, options={"default": 0})
     */
    protected $mustResetPassword = 0;

    /**
     * Partner contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="partner_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $partnerContactDetails;

    /**
     * Password expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="password_expiry_date", nullable=true)
     */
    protected $passwordExpiryDate;

    /**
     * Password reminder sent
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="password_reminder_sent", nullable=true)
     */
    protected $passwordReminderSent;

    /**
     * Pid
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pid", length=255, nullable=true)
     */
    protected $pid;

    /**
     * Reset password expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="reset_password_expiry_date", nullable=true)
     */
    protected $resetPasswordExpiryDate;

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
     * Set the hint question1
     *
     * @param \Olcs\Db\Entity\HintQuestion $hintQuestion1
     * @return User
     */
    public function setHintQuestion1($hintQuestion1)
    {
        $this->hintQuestion1 = $hintQuestion1;

        return $this;
    }

    /**
     * Get the hint question1
     *
     * @return \Olcs\Db\Entity\HintQuestion
     */
    public function getHintQuestion1()
    {
        return $this->hintQuestion1;
    }

    /**
     * Set the hint question2
     *
     * @param \Olcs\Db\Entity\HintQuestion $hintQuestion2
     * @return User
     */
    public function setHintQuestion2($hintQuestion2)
    {
        $this->hintQuestion2 = $hintQuestion2;

        return $this;
    }

    /**
     * Get the hint question2
     *
     * @return \Olcs\Db\Entity\HintQuestion
     */
    public function getHintQuestion2()
    {
        return $this->hintQuestion2;
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
     * Set the pid
     *
     * @param string $pid
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
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
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
