<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationCompletion Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_completion",
 *    indexes={
 *        @ORM\Index(name="fk_application_completion_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_completion_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ApplicationCompletion implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Application")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * Section yb status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_yb_status", nullable=true)
     */
    protected $sectionYbStatus;

    /**
     * Section yb bt status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_yb_bt_status", nullable=true)
     */
    protected $sectionYbBtStatus;

    /**
     * Section yb bd status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_yb_bd_status", nullable=true)
     */
    protected $sectionYbBdStatus;

    /**
     * Section yb add status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_yb_add_status", nullable=true)
     */
    protected $sectionYbAddStatus;

    /**
     * Section yb peo status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_yb_peo_status", nullable=true)
     */
    protected $sectionYbPeoStatus;

    /**
     * Section tol status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tol_status", nullable=true)
     */
    protected $sectionTolStatus;

    /**
     * Section tol ol status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tol_ol_status", nullable=true)
     */
    protected $sectionTolOlStatus;

    /**
     * Section tol ot status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tol_ot_status", nullable=true)
     */
    protected $sectionTolOtStatus;

    /**
     * Section tol lt status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tol_lt_status", nullable=true)
     */
    protected $sectionTolLtStatus;

    /**
     * Section ocs status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ocs_status", nullable=true)
     */
    protected $sectionOcsStatus;

    /**
     * Section ocs auth status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ocs_auth_status", nullable=true)
     */
    protected $sectionOcsAuthStatus;

    /**
     * Section ocs fe status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ocs_fe_status", nullable=true)
     */
    protected $sectionOcsFeStatus;

    /**
     * Section tms status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tms_status", nullable=true)
     */
    protected $sectionTmsStatus;

    /**
     * Section veh status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_veh_status", nullable=true)
     */
    protected $sectionVehStatus;

    /**
     * Section veh v status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_veh_v_status", nullable=true)
     */
    protected $sectionVehVStatus;

    /**
     * Section veh vpsv status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_veh_vpsv_status", nullable=true)
     */
    protected $sectionVehVpsvStatus;

    /**
     * Section veh s status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_veh_s_status", nullable=true)
     */
    protected $sectionVehSStatus;

    /**
     * Section ph status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ph_status", nullable=true)
     */
    protected $sectionPhStatus;

    /**
     * Section ph fh status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ph_fh_status", nullable=true)
     */
    protected $sectionPhFhStatus;

    /**
     * Section ph lh status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ph_lh_status", nullable=true)
     */
    protected $sectionPhLhStatus;

    /**
     * Section ph cp status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_ph_cp_status", nullable=true)
     */
    protected $sectionPhCpStatus;

    /**
     * Section rd status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_rd_status", nullable=true)
     */
    protected $sectionRdStatus;

    /**
     * Section pay status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_pay_status", nullable=true)
     */
    protected $sectionPayStatus;

    /**
     * Section sub status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_sub_status", nullable=true)
     */
    protected $sectionSubStatus;

    /**
     * Section pay pay status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_pay_pay_status", nullable=true)
     */
    protected $sectionPayPayStatus;

    /**
     * Section pay summary status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_pay_summary_status", nullable=true)
     */
    protected $sectionPaySummaryStatus;

    /**
     * Section tp lic status
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="section_tp_lic_status", nullable=true)
     */
    protected $sectionTpLicStatus;

    /**
     * Last section
     *
     * @var string
     *
     * @ORM\Column(type="string", name="last_section", length=255, nullable=true)
     */
    protected $lastSection;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the section yb status
     *
     * @param boolean $sectionYbStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionYbStatus($sectionYbStatus)
    {
        $this->sectionYbStatus = $sectionYbStatus;

        return $this;
    }

    /**
     * Get the section yb status
     *
     * @return boolean
     */
    public function getSectionYbStatus()
    {
        return $this->sectionYbStatus;
    }

    /**
     * Set the section yb bt status
     *
     * @param boolean $sectionYbBtStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionYbBtStatus($sectionYbBtStatus)
    {
        $this->sectionYbBtStatus = $sectionYbBtStatus;

        return $this;
    }

    /**
     * Get the section yb bt status
     *
     * @return boolean
     */
    public function getSectionYbBtStatus()
    {
        return $this->sectionYbBtStatus;
    }

    /**
     * Set the section yb bd status
     *
     * @param boolean $sectionYbBdStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionYbBdStatus($sectionYbBdStatus)
    {
        $this->sectionYbBdStatus = $sectionYbBdStatus;

        return $this;
    }

    /**
     * Get the section yb bd status
     *
     * @return boolean
     */
    public function getSectionYbBdStatus()
    {
        return $this->sectionYbBdStatus;
    }

    /**
     * Set the section yb add status
     *
     * @param boolean $sectionYbAddStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionYbAddStatus($sectionYbAddStatus)
    {
        $this->sectionYbAddStatus = $sectionYbAddStatus;

        return $this;
    }

    /**
     * Get the section yb add status
     *
     * @return boolean
     */
    public function getSectionYbAddStatus()
    {
        return $this->sectionYbAddStatus;
    }

    /**
     * Set the section yb peo status
     *
     * @param boolean $sectionYbPeoStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionYbPeoStatus($sectionYbPeoStatus)
    {
        $this->sectionYbPeoStatus = $sectionYbPeoStatus;

        return $this;
    }

    /**
     * Get the section yb peo status
     *
     * @return boolean
     */
    public function getSectionYbPeoStatus()
    {
        return $this->sectionYbPeoStatus;
    }

    /**
     * Set the section tol status
     *
     * @param boolean $sectionTolStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTolStatus($sectionTolStatus)
    {
        $this->sectionTolStatus = $sectionTolStatus;

        return $this;
    }

    /**
     * Get the section tol status
     *
     * @return boolean
     */
    public function getSectionTolStatus()
    {
        return $this->sectionTolStatus;
    }

    /**
     * Set the section tol ol status
     *
     * @param boolean $sectionTolOlStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTolOlStatus($sectionTolOlStatus)
    {
        $this->sectionTolOlStatus = $sectionTolOlStatus;

        return $this;
    }

    /**
     * Get the section tol ol status
     *
     * @return boolean
     */
    public function getSectionTolOlStatus()
    {
        return $this->sectionTolOlStatus;
    }

    /**
     * Set the section tol ot status
     *
     * @param boolean $sectionTolOtStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTolOtStatus($sectionTolOtStatus)
    {
        $this->sectionTolOtStatus = $sectionTolOtStatus;

        return $this;
    }

    /**
     * Get the section tol ot status
     *
     * @return boolean
     */
    public function getSectionTolOtStatus()
    {
        return $this->sectionTolOtStatus;
    }

    /**
     * Set the section tol lt status
     *
     * @param boolean $sectionTolLtStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTolLtStatus($sectionTolLtStatus)
    {
        $this->sectionTolLtStatus = $sectionTolLtStatus;

        return $this;
    }

    /**
     * Get the section tol lt status
     *
     * @return boolean
     */
    public function getSectionTolLtStatus()
    {
        return $this->sectionTolLtStatus;
    }

    /**
     * Set the section ocs status
     *
     * @param boolean $sectionOcsStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionOcsStatus($sectionOcsStatus)
    {
        $this->sectionOcsStatus = $sectionOcsStatus;

        return $this;
    }

    /**
     * Get the section ocs status
     *
     * @return boolean
     */
    public function getSectionOcsStatus()
    {
        return $this->sectionOcsStatus;
    }

    /**
     * Set the section ocs auth status
     *
     * @param boolean $sectionOcsAuthStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionOcsAuthStatus($sectionOcsAuthStatus)
    {
        $this->sectionOcsAuthStatus = $sectionOcsAuthStatus;

        return $this;
    }

    /**
     * Get the section ocs auth status
     *
     * @return boolean
     */
    public function getSectionOcsAuthStatus()
    {
        return $this->sectionOcsAuthStatus;
    }

    /**
     * Set the section ocs fe status
     *
     * @param boolean $sectionOcsFeStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionOcsFeStatus($sectionOcsFeStatus)
    {
        $this->sectionOcsFeStatus = $sectionOcsFeStatus;

        return $this;
    }

    /**
     * Get the section ocs fe status
     *
     * @return boolean
     */
    public function getSectionOcsFeStatus()
    {
        return $this->sectionOcsFeStatus;
    }

    /**
     * Set the section tms status
     *
     * @param boolean $sectionTmsStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTmsStatus($sectionTmsStatus)
    {
        $this->sectionTmsStatus = $sectionTmsStatus;

        return $this;
    }

    /**
     * Get the section tms status
     *
     * @return boolean
     */
    public function getSectionTmsStatus()
    {
        return $this->sectionTmsStatus;
    }

    /**
     * Set the section veh status
     *
     * @param boolean $sectionVehStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionVehStatus($sectionVehStatus)
    {
        $this->sectionVehStatus = $sectionVehStatus;

        return $this;
    }

    /**
     * Get the section veh status
     *
     * @return boolean
     */
    public function getSectionVehStatus()
    {
        return $this->sectionVehStatus;
    }

    /**
     * Set the section veh v status
     *
     * @param boolean $sectionVehVStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionVehVStatus($sectionVehVStatus)
    {
        $this->sectionVehVStatus = $sectionVehVStatus;

        return $this;
    }

    /**
     * Get the section veh v status
     *
     * @return boolean
     */
    public function getSectionVehVStatus()
    {
        return $this->sectionVehVStatus;
    }

    /**
     * Set the section veh vpsv status
     *
     * @param boolean $sectionVehVpsvStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionVehVpsvStatus($sectionVehVpsvStatus)
    {
        $this->sectionVehVpsvStatus = $sectionVehVpsvStatus;

        return $this;
    }

    /**
     * Get the section veh vpsv status
     *
     * @return boolean
     */
    public function getSectionVehVpsvStatus()
    {
        return $this->sectionVehVpsvStatus;
    }

    /**
     * Set the section veh s status
     *
     * @param boolean $sectionVehSStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionVehSStatus($sectionVehSStatus)
    {
        $this->sectionVehSStatus = $sectionVehSStatus;

        return $this;
    }

    /**
     * Get the section veh s status
     *
     * @return boolean
     */
    public function getSectionVehSStatus()
    {
        return $this->sectionVehSStatus;
    }

    /**
     * Set the section ph status
     *
     * @param boolean $sectionPhStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPhStatus($sectionPhStatus)
    {
        $this->sectionPhStatus = $sectionPhStatus;

        return $this;
    }

    /**
     * Get the section ph status
     *
     * @return boolean
     */
    public function getSectionPhStatus()
    {
        return $this->sectionPhStatus;
    }

    /**
     * Set the section ph fh status
     *
     * @param boolean $sectionPhFhStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPhFhStatus($sectionPhFhStatus)
    {
        $this->sectionPhFhStatus = $sectionPhFhStatus;

        return $this;
    }

    /**
     * Get the section ph fh status
     *
     * @return boolean
     */
    public function getSectionPhFhStatus()
    {
        return $this->sectionPhFhStatus;
    }

    /**
     * Set the section ph lh status
     *
     * @param boolean $sectionPhLhStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPhLhStatus($sectionPhLhStatus)
    {
        $this->sectionPhLhStatus = $sectionPhLhStatus;

        return $this;
    }

    /**
     * Get the section ph lh status
     *
     * @return boolean
     */
    public function getSectionPhLhStatus()
    {
        return $this->sectionPhLhStatus;
    }

    /**
     * Set the section ph cp status
     *
     * @param boolean $sectionPhCpStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPhCpStatus($sectionPhCpStatus)
    {
        $this->sectionPhCpStatus = $sectionPhCpStatus;

        return $this;
    }

    /**
     * Get the section ph cp status
     *
     * @return boolean
     */
    public function getSectionPhCpStatus()
    {
        return $this->sectionPhCpStatus;
    }

    /**
     * Set the section rd status
     *
     * @param boolean $sectionRdStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionRdStatus($sectionRdStatus)
    {
        $this->sectionRdStatus = $sectionRdStatus;

        return $this;
    }

    /**
     * Get the section rd status
     *
     * @return boolean
     */
    public function getSectionRdStatus()
    {
        return $this->sectionRdStatus;
    }

    /**
     * Set the section pay status
     *
     * @param boolean $sectionPayStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPayStatus($sectionPayStatus)
    {
        $this->sectionPayStatus = $sectionPayStatus;

        return $this;
    }

    /**
     * Get the section pay status
     *
     * @return boolean
     */
    public function getSectionPayStatus()
    {
        return $this->sectionPayStatus;
    }

    /**
     * Set the section sub status
     *
     * @param boolean $sectionSubStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionSubStatus($sectionSubStatus)
    {
        $this->sectionSubStatus = $sectionSubStatus;

        return $this;
    }

    /**
     * Get the section sub status
     *
     * @return boolean
     */
    public function getSectionSubStatus()
    {
        return $this->sectionSubStatus;
    }

    /**
     * Set the section pay pay status
     *
     * @param boolean $sectionPayPayStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPayPayStatus($sectionPayPayStatus)
    {
        $this->sectionPayPayStatus = $sectionPayPayStatus;

        return $this;
    }

    /**
     * Get the section pay pay status
     *
     * @return boolean
     */
    public function getSectionPayPayStatus()
    {
        return $this->sectionPayPayStatus;
    }

    /**
     * Set the section pay summary status
     *
     * @param boolean $sectionPaySummaryStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionPaySummaryStatus($sectionPaySummaryStatus)
    {
        $this->sectionPaySummaryStatus = $sectionPaySummaryStatus;

        return $this;
    }

    /**
     * Get the section pay summary status
     *
     * @return boolean
     */
    public function getSectionPaySummaryStatus()
    {
        return $this->sectionPaySummaryStatus;
    }

    /**
     * Set the section tp lic status
     *
     * @param boolean $sectionTpLicStatus
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setSectionTpLicStatus($sectionTpLicStatus)
    {
        $this->sectionTpLicStatus = $sectionTpLicStatus;

        return $this;
    }

    /**
     * Get the section tp lic status
     *
     * @return boolean
     */
    public function getSectionTpLicStatus()
    {
        return $this->sectionTpLicStatus;
    }

    /**
     * Set the last section
     *
     * @param string $lastSection
     * @return \Olcs\Db\Entity\ApplicationCompletion
     */
    public function setLastSection($lastSection)
    {
        $this->lastSection = $lastSection;

        return $this;
    }

    /**
     * Get the last section
     *
     * @return string
     */
    public function getLastSection()
    {
        return $this->lastSection;
    }
}
