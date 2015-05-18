<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
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
 *        @ORM\Index(name="ix_user_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_user_organisation_id", columns={"organisation_id"})
 *    }
 * )
 */
abstract class AbstractUser
{

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
     * Contact details
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $contactDetails;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Department name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="department_name", length=100, nullable=true)
     */
    protected $departmentName;

    /**
     * Division group
     *
     * @var string
     *
     * @ORM\Column(type="string", name="division_group", length=100, nullable=true)
     */
    protected $divisionGroup;

    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=45, nullable=true)
     */
    protected $emailAddress;

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
     * @var \Dvsa\Olcs\Api\Entity\User\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\HintQuestion", fetch="LAZY")
     * @ORM\JoinColumn(name="hint_question_id1", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestion1;

    /**
     * Hint question2
     *
     * @var \Dvsa\Olcs\Api\Entity\User\HintQuestion
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\HintQuestion", fetch="LAZY")
     * @ORM\JoinColumn(name="hint_question_id2", referencedColumnName="id", nullable=true)
     */
    protected $hintQuestion2;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Job title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="job_title", length=100, nullable=true)
     */
    protected $jobTitle;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Last successful login date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_successful_login_date", nullable=true)
     */
    protected $lastSuccessfulLoginDate;

    /**
     * Local authority
     *
     * @var \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Bus\LocalAuthority", fetch="LAZY")
     * @ORM\JoinColumn(name="local_authority_id", referencedColumnName="id", nullable=true)
     */
    protected $localAuthority;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="must_reset_password", nullable=false, options={"default": 0})
     */
    protected $mustResetPassword = 0;

    /**
     * Organisation
     *
     * @var \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Organisation\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true)
     */
    protected $organisation;

    /**
     * Partner contact details
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails", fetch="LAZY")
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
     * Team
     *
     * @var \Dvsa\Olcs\Api\Entity\User\Team
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     */
    protected $team;

    /**
     * Transport manager
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManager", fetch="LAZY")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManager;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 0})
     * @ORM\Version
     */
    protected $version = 0;

    /**
     * Organisation user
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser", mappedBy="user")
     */
    protected $organisationUsers;

    /**
     * User role
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\User\UserRole", mappedBy="user", cascade={"persist"})
     */
    protected $userRoles;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->organisationUsers = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
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
     * Set the contact details
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $contactDetails
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
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return User
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return User
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return User
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
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
     * Set the email address
     *
     * @param string $emailAddress
     * @return User
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
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
     * @param \Dvsa\Olcs\Api\Entity\User\HintQuestion $hintQuestion1
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
     * @return \Dvsa\Olcs\Api\Entity\User\HintQuestion
     */
    public function getHintQuestion1()
    {
        return $this->hintQuestion1;
    }

    /**
     * Set the hint question2
     *
     * @param \Dvsa\Olcs\Api\Entity\User\HintQuestion $hintQuestion2
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
     * @return \Dvsa\Olcs\Api\Entity\User\HintQuestion
     */
    public function getHintQuestion2()
    {
        return $this->hintQuestion2;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return User
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return User
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
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
     * Set the local authority
     *
     * @param \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority $localAuthority
     * @return User
     */
    public function setLocalAuthority($localAuthority)
    {
        $this->localAuthority = $localAuthority;

        return $this;
    }

    /**
     * Get the local authority
     *
     * @return \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority
     */
    public function getLocalAuthority()
    {
        return $this->localAuthority;
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
     * @param string $mustResetPassword
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
     * @return string
     */
    public function getMustResetPassword()
    {
        return $this->mustResetPassword;
    }

    /**
     * Set the organisation
     *
     * @param \Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     * @return User
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the partner contact details
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $partnerContactDetails
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
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
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
     * Set the team
     *
     * @param \Dvsa\Olcs\Api\Entity\User\Team $team
     * @return User
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Dvsa\Olcs\Api\Entity\User\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the transport manager
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManager $transportManager
     * @return User
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return User
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
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

    /**
     * Set the user role
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $userRoles
     * @return User
     */
    public function setUserRoles($userRoles)
    {
        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * Get the user roles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * Add a user roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $userRoles
     * @return User
     */
    public function addUserRoles($userRoles)
    {
        if ($userRoles instanceof ArrayCollection) {
            $this->userRoles = new ArrayCollection(
                array_merge(
                    $this->userRoles->toArray(),
                    $userRoles->toArray()
                )
            );
        } elseif (!$this->userRoles->contains($userRoles)) {
            $this->userRoles->add($userRoles);
        }

        return $this;
    }

    /**
     * Remove a user roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $userRoles
     * @return User
     */
    public function removeUserRoles($userRoles)
    {
        if ($this->userRoles->contains($userRoles)) {
            $this->userRoles->removeElement($userRoles);
        }

        return $this;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
