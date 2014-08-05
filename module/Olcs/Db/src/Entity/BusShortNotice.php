<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusShortNotice Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_short_notice",
 *    indexes={
 *        @ORM\Index(name="fk_bus_short_notice_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_short_notice_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class BusShortNotice implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\BusRegOneToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Bank holiday change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="bank_holiday_change", nullable=false)
     */
    protected $bankHolidayChange = 0;

    /**
     * Unforseen change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="unforseen_change", nullable=false)
     */
    protected $unforseenChange = 0;

    /**
     * Unforseen detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="unforseen_detail", length=255, nullable=true)
     */
    protected $unforseenDetail;

    /**
     * Timetable change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="timetable_change", nullable=false)
     */
    protected $timetableChange = 0;

    /**
     * Timetable detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="timetable_detail", length=255, nullable=true)
     */
    protected $timetableDetail;

    /**
     * Replacement change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="replacement_change", nullable=false)
     */
    protected $replacementChange = 0;

    /**
     * Replacement detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="replacement_detail", length=255, nullable=true)
     */
    protected $replacementDetail;

    /**
     * Holiday change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="holiday_change", nullable=false)
     */
    protected $holidayChange = 0;

    /**
     * Holiday detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="holiday_detail", length=255, nullable=true)
     */
    protected $holidayDetail;

    /**
     * Trc change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="trc_change", nullable=false)
     */
    protected $trcChange = 0;

    /**
     * Trc detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="trc_detail", length=255, nullable=true)
     */
    protected $trcDetail;

    /**
     * Police change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="police_change", nullable=false)
     */
    protected $policeChange = 0;

    /**
     * Police detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="police_detail", length=255, nullable=true)
     */
    protected $policeDetail;

    /**
     * Special occasion change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="special_occasion_change", nullable=false)
     */
    protected $specialOccasionChange = 0;

    /**
     * Special occasion detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="special_occasion_detail", length=255, nullable=true)
     */
    protected $specialOccasionDetail;

    /**
     * Connection change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="connection_change", nullable=false)
     */
    protected $connectionChange = 0;

    /**
     * Connection detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="connection_detail", length=255, nullable=true)
     */
    protected $connectionDetail;

    /**
     * Not available change
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="not_available_change", nullable=false)
     */
    protected $notAvailableChange = 0;

    /**
     * Not available detail
     *
     * @var string
     *
     * @ORM\Column(type="string", name="not_available_detail", length=255, nullable=true)
     */
    protected $notAvailableDetail;

    /**
     * Set the bank holiday change
     *
     * @param boolean $bankHolidayChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setBankHolidayChange($bankHolidayChange)
    {
        $this->bankHolidayChange = $bankHolidayChange;

        return $this;
    }

    /**
     * Get the bank holiday change
     *
     * @return boolean
     */
    public function getBankHolidayChange()
    {
        return $this->bankHolidayChange;
    }

    /**
     * Set the unforseen change
     *
     * @param boolean $unforseenChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setUnforseenChange($unforseenChange)
    {
        $this->unforseenChange = $unforseenChange;

        return $this;
    }

    /**
     * Get the unforseen change
     *
     * @return boolean
     */
    public function getUnforseenChange()
    {
        return $this->unforseenChange;
    }

    /**
     * Set the unforseen detail
     *
     * @param string $unforseenDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setUnforseenDetail($unforseenDetail)
    {
        $this->unforseenDetail = $unforseenDetail;

        return $this;
    }

    /**
     * Get the unforseen detail
     *
     * @return string
     */
    public function getUnforseenDetail()
    {
        return $this->unforseenDetail;
    }

    /**
     * Set the timetable change
     *
     * @param boolean $timetableChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setTimetableChange($timetableChange)
    {
        $this->timetableChange = $timetableChange;

        return $this;
    }

    /**
     * Get the timetable change
     *
     * @return boolean
     */
    public function getTimetableChange()
    {
        return $this->timetableChange;
    }

    /**
     * Set the timetable detail
     *
     * @param string $timetableDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setTimetableDetail($timetableDetail)
    {
        $this->timetableDetail = $timetableDetail;

        return $this;
    }

    /**
     * Get the timetable detail
     *
     * @return string
     */
    public function getTimetableDetail()
    {
        return $this->timetableDetail;
    }

    /**
     * Set the replacement change
     *
     * @param boolean $replacementChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setReplacementChange($replacementChange)
    {
        $this->replacementChange = $replacementChange;

        return $this;
    }

    /**
     * Get the replacement change
     *
     * @return boolean
     */
    public function getReplacementChange()
    {
        return $this->replacementChange;
    }

    /**
     * Set the replacement detail
     *
     * @param string $replacementDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setReplacementDetail($replacementDetail)
    {
        $this->replacementDetail = $replacementDetail;

        return $this;
    }

    /**
     * Get the replacement detail
     *
     * @return string
     */
    public function getReplacementDetail()
    {
        return $this->replacementDetail;
    }

    /**
     * Set the holiday change
     *
     * @param boolean $holidayChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setHolidayChange($holidayChange)
    {
        $this->holidayChange = $holidayChange;

        return $this;
    }

    /**
     * Get the holiday change
     *
     * @return boolean
     */
    public function getHolidayChange()
    {
        return $this->holidayChange;
    }

    /**
     * Set the holiday detail
     *
     * @param string $holidayDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setHolidayDetail($holidayDetail)
    {
        $this->holidayDetail = $holidayDetail;

        return $this;
    }

    /**
     * Get the holiday detail
     *
     * @return string
     */
    public function getHolidayDetail()
    {
        return $this->holidayDetail;
    }

    /**
     * Set the trc change
     *
     * @param boolean $trcChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setTrcChange($trcChange)
    {
        $this->trcChange = $trcChange;

        return $this;
    }

    /**
     * Get the trc change
     *
     * @return boolean
     */
    public function getTrcChange()
    {
        return $this->trcChange;
    }

    /**
     * Set the trc detail
     *
     * @param string $trcDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setTrcDetail($trcDetail)
    {
        $this->trcDetail = $trcDetail;

        return $this;
    }

    /**
     * Get the trc detail
     *
     * @return string
     */
    public function getTrcDetail()
    {
        return $this->trcDetail;
    }

    /**
     * Set the police change
     *
     * @param boolean $policeChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setPoliceChange($policeChange)
    {
        $this->policeChange = $policeChange;

        return $this;
    }

    /**
     * Get the police change
     *
     * @return boolean
     */
    public function getPoliceChange()
    {
        return $this->policeChange;
    }

    /**
     * Set the police detail
     *
     * @param string $policeDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setPoliceDetail($policeDetail)
    {
        $this->policeDetail = $policeDetail;

        return $this;
    }

    /**
     * Get the police detail
     *
     * @return string
     */
    public function getPoliceDetail()
    {
        return $this->policeDetail;
    }

    /**
     * Set the special occasion change
     *
     * @param boolean $specialOccasionChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setSpecialOccasionChange($specialOccasionChange)
    {
        $this->specialOccasionChange = $specialOccasionChange;

        return $this;
    }

    /**
     * Get the special occasion change
     *
     * @return boolean
     */
    public function getSpecialOccasionChange()
    {
        return $this->specialOccasionChange;
    }

    /**
     * Set the special occasion detail
     *
     * @param string $specialOccasionDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setSpecialOccasionDetail($specialOccasionDetail)
    {
        $this->specialOccasionDetail = $specialOccasionDetail;

        return $this;
    }

    /**
     * Get the special occasion detail
     *
     * @return string
     */
    public function getSpecialOccasionDetail()
    {
        return $this->specialOccasionDetail;
    }

    /**
     * Set the connection change
     *
     * @param boolean $connectionChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setConnectionChange($connectionChange)
    {
        $this->connectionChange = $connectionChange;

        return $this;
    }

    /**
     * Get the connection change
     *
     * @return boolean
     */
    public function getConnectionChange()
    {
        return $this->connectionChange;
    }

    /**
     * Set the connection detail
     *
     * @param string $connectionDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setConnectionDetail($connectionDetail)
    {
        $this->connectionDetail = $connectionDetail;

        return $this;
    }

    /**
     * Get the connection detail
     *
     * @return string
     */
    public function getConnectionDetail()
    {
        return $this->connectionDetail;
    }

    /**
     * Set the not available change
     *
     * @param boolean $notAvailableChange
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setNotAvailableChange($notAvailableChange)
    {
        $this->notAvailableChange = $notAvailableChange;

        return $this;
    }

    /**
     * Get the not available change
     *
     * @return boolean
     */
    public function getNotAvailableChange()
    {
        return $this->notAvailableChange;
    }

    /**
     * Set the not available detail
     *
     * @param string $notAvailableDetail
     * @return \Olcs\Db\Entity\BusShortNotice
     */
    public function setNotAvailableDetail($notAvailableDetail)
    {
        $this->notAvailableDetail = $notAvailableDetail;

        return $this;
    }

    /**
     * Get the not available detail
     *
     * @return string
     */
    public function getNotAvailableDetail()
    {
        return $this->notAvailableDetail;
    }
}
