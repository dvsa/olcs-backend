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
 *        @ORM\Index(name="IDX_9C4781CEDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_9C4781CE65CF370E", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="bus_reg_id_UNIQUE", columns={"bus_reg_id"})
 *    }
 * )
 */
class BusShortNotice implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\BusRegManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Bank holiday change
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="bank_holiday_change", nullable=false)
     */
    protected $bankHolidayChange;

    /**
     * Unforseen change
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="unforseen_change", nullable=false)
     */
    protected $unforseenChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="timetable_change", nullable=false)
     */
    protected $timetableChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="replacement_change", nullable=false)
     */
    protected $replacementChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="holiday_change", nullable=false)
     */
    protected $holidayChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="trc_change", nullable=false)
     */
    protected $trcChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="police_change", nullable=false)
     */
    protected $policeChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="special_occasion_change", nullable=false)
     */
    protected $specialOccasionChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="connection_change", nullable=false)
     */
    protected $connectionChange;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="not_available_change", nullable=false)
     */
    protected $notAvailableChange;

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
     * @param string $bankHolidayChange
     * @return BusShortNotice
     */
    public function setBankHolidayChange($bankHolidayChange)
    {
        $this->bankHolidayChange = $bankHolidayChange;

        return $this;
    }

    /**
     * Get the bank holiday change
     *
     * @return string
     */
    public function getBankHolidayChange()
    {
        return $this->bankHolidayChange;
    }

    /**
     * Set the unforseen change
     *
     * @param string $unforseenChange
     * @return BusShortNotice
     */
    public function setUnforseenChange($unforseenChange)
    {
        $this->unforseenChange = $unforseenChange;

        return $this;
    }

    /**
     * Get the unforseen change
     *
     * @return string
     */
    public function getUnforseenChange()
    {
        return $this->unforseenChange;
    }

    /**
     * Set the unforseen detail
     *
     * @param string $unforseenDetail
     * @return BusShortNotice
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
     * @param string $timetableChange
     * @return BusShortNotice
     */
    public function setTimetableChange($timetableChange)
    {
        $this->timetableChange = $timetableChange;

        return $this;
    }

    /**
     * Get the timetable change
     *
     * @return string
     */
    public function getTimetableChange()
    {
        return $this->timetableChange;
    }

    /**
     * Set the timetable detail
     *
     * @param string $timetableDetail
     * @return BusShortNotice
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
     * @param string $replacementChange
     * @return BusShortNotice
     */
    public function setReplacementChange($replacementChange)
    {
        $this->replacementChange = $replacementChange;

        return $this;
    }

    /**
     * Get the replacement change
     *
     * @return string
     */
    public function getReplacementChange()
    {
        return $this->replacementChange;
    }

    /**
     * Set the replacement detail
     *
     * @param string $replacementDetail
     * @return BusShortNotice
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
     * @param string $holidayChange
     * @return BusShortNotice
     */
    public function setHolidayChange($holidayChange)
    {
        $this->holidayChange = $holidayChange;

        return $this;
    }

    /**
     * Get the holiday change
     *
     * @return string
     */
    public function getHolidayChange()
    {
        return $this->holidayChange;
    }

    /**
     * Set the holiday detail
     *
     * @param string $holidayDetail
     * @return BusShortNotice
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
     * @param string $trcChange
     * @return BusShortNotice
     */
    public function setTrcChange($trcChange)
    {
        $this->trcChange = $trcChange;

        return $this;
    }

    /**
     * Get the trc change
     *
     * @return string
     */
    public function getTrcChange()
    {
        return $this->trcChange;
    }

    /**
     * Set the trc detail
     *
     * @param string $trcDetail
     * @return BusShortNotice
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
     * @param string $policeChange
     * @return BusShortNotice
     */
    public function setPoliceChange($policeChange)
    {
        $this->policeChange = $policeChange;

        return $this;
    }

    /**
     * Get the police change
     *
     * @return string
     */
    public function getPoliceChange()
    {
        return $this->policeChange;
    }

    /**
     * Set the police detail
     *
     * @param string $policeDetail
     * @return BusShortNotice
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
     * @param string $specialOccasionChange
     * @return BusShortNotice
     */
    public function setSpecialOccasionChange($specialOccasionChange)
    {
        $this->specialOccasionChange = $specialOccasionChange;

        return $this;
    }

    /**
     * Get the special occasion change
     *
     * @return string
     */
    public function getSpecialOccasionChange()
    {
        return $this->specialOccasionChange;
    }

    /**
     * Set the special occasion detail
     *
     * @param string $specialOccasionDetail
     * @return BusShortNotice
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
     * @param string $connectionChange
     * @return BusShortNotice
     */
    public function setConnectionChange($connectionChange)
    {
        $this->connectionChange = $connectionChange;

        return $this;
    }

    /**
     * Get the connection change
     *
     * @return string
     */
    public function getConnectionChange()
    {
        return $this->connectionChange;
    }

    /**
     * Set the connection detail
     *
     * @param string $connectionDetail
     * @return BusShortNotice
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
     * @param string $notAvailableChange
     * @return BusShortNotice
     */
    public function setNotAvailableChange($notAvailableChange)
    {
        $this->notAvailableChange = $notAvailableChange;

        return $this;
    }

    /**
     * Get the not available change
     *
     * @return string
     */
    public function getNotAvailableChange()
    {
        return $this->notAvailableChange;
    }

    /**
     * Set the not available detail
     *
     * @param string $notAvailableDetail
     * @return BusShortNotice
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
