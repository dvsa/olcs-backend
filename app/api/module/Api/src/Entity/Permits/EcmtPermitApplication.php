<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * EcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_permit_type", columns={"permit_type"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_sectors_id", columns={"sectors_id"})
 *    }
 * )
 */
class EcmtPermitApplication extends AbstractEcmtPermitApplication
{
    const STATUS_CANCELLED = 'ecmt_permit_cancelled';
    const STATUS_NOT_YET_SUBMITTED = 'ecmt_permit_nys';
    const STATUS_UNDER_CONSIDERATION = 'ecmt_permit_uc';
    const STATUS_WITHDRAWN = 'ecmt_permit_withdrawn';
    const STATUS_AWAITING_FEE = 'ecmt_permit_awaiting';
    const STATUS_UNSUCCESSFUL = 'ecmt_permit_unsuccessful';
    const STATUS_ISSUED = 'ecmt_permit_issued';

    const PERMIT_TYPE = 'permit_ecmt';

    const SECTION_COMPLETION_CANNOT_START = 'ecmt_section_sts_csy';
    const SECTION_COMPLETION_NOT_STARTED = 'ecmt_section_sts_nys';
    const SECTION_COMPLETION_COMPLETED = 'ecmt_section_sts_com';

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    const SECTIONS = [
        'licence' => 'fieldIsNotNull',
        'emissions' => 'fieldIsAgreed',
        'cabotage' => 'fieldIsAgreed',
        'internationalJourneys' => 'fieldIsNotNull',
        'trips' => 'fieldIsInt',
        'permitsRequired' => 'fieldIsInt',
        'sectors' => 'fieldIsNotNull',
        'countrys' => 'collectionHasRecord',
    ];

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    const CONFIRMATION_SECTIONS = [
        'checkedAnswers' => 'fieldIsAgreed',
        'declaration' => 'fieldIsAgreed',
    ];

    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData $status        Status
     * @param RefData $paymentStatus Payment status
     * @param RefData $permitType    Permit type
     * @param Licence $licence       Licence
     *
     * @return EcmtPermitApplication
     */
    public static function createNew(
        RefData $status,
        RefData $paymentStatus,
        RefData $permitType,
        Licence $licence
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->setStatus($status);
        $ecmtPermitApplication->setPaymentStatus($paymentStatus); //@todo drop payment status column
        $ecmtPermitApplication->setPermitType($permitType);
        $ecmtPermitApplication->setLicence($licence);

        return $ecmtPermitApplication;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'applicationRef' => $this->getApplicationRef(),
            'canBeCancelled' => $this->canBeCancelled(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'confirmationSectionCompletion' => $this->getSectionCompletion(self::CONFIRMATION_SECTIONS),
            'sectionCompletion' => $this->getSectionCompletion(self::SECTIONS),
        ];
    }

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    private function getSectionCompletion($sections)
    {
        $sectionCompletion = [];
        $totalCompleted = 0;
        $totalSections = count($sections);

        foreach ($sections as $field => $validator) {
            //default field to not started
            $status = self::SECTION_COMPLETION_NOT_STARTED;
            $fieldCompleted = $this->$validator($field);

            //if field completed, increment the completed number, and set the status
            if ($fieldCompleted) {
                $totalCompleted++;
                $status = self::SECTION_COMPLETION_COMPLETED;
            }

            $sectionCompletion[$field] = $status;
        }

        $sectionCompletion['totalSections'] = $totalSections;
        $sectionCompletion['totalCompleted'] = $totalCompleted;
        $sectionCompletion['allCompleted'] = ($totalSections === $totalCompleted);

        return $sectionCompletion;
    }

    /**
     * Checks an array collection has records
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function collectionHasRecord($field)
    {
        return (bool)$this->$field->count();
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsAgreed($field)
    {
        return $this->$field == true;
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsNotNull($field)
    {
        return $this->$field !== null;
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsInt($field)
    {
        return is_int($this->$field);
    }

    /**
     * Get the application reference
     *
     * @return string
     */
    public function getApplicationRef()
    {
        return $this->licence->getLicNo() . ' / ' . $this->id;
    }

    public function isNotYetSubmitted()
    {
        return $this->status->getId() === self::STATUS_NOT_YET_SUBMITTED;
    }

    /**
     * Whether the permit application can be cancelled
     * @todo this currently reruns the section completion checks, should store the value instead for speed
     *
     * @return bool
     */
    private function canBeSubmitted()
    {
        if (!$this->isNotYetSubmitted()) {
            return false;
        }

        $sections = $this->getSectionCompletion(self::CONFIRMATION_SECTIONS);

        if (!$sections['allCompleted']) {
            return false;
        }

        $sections = $this->getSectionCompletion(self::SECTIONS);

        return $sections['allCompleted'];
    }

    /**
     * Whether the permit application can be cancelled
     *
     * @return bool
     */
    private function canBeCancelled()
    {
        return $this->status->getId() === self::STATUS_NOT_YET_SUBMITTED;
    }
}
