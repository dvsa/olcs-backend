<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
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
class EcmtPermitApplication extends AbstractEcmtPermitApplication implements OrganisationProviderInterface
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

    const INTER_JOURNEY_LESS_60 = 'inter_journey_less_60';
    const INTER_JOURNEY_60_90 = 'inter_journey_60_90';
    const INTER_JOURNEY_MORE_90 = 'inter_journey_more_90';

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
        'countrys' => 'countrysPopulated',
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
     * @param RefData $status Status
     * @param RefData $paymentStatus Payment status
     * @param RefData $permitType Permit type
     * @param Licence $licence Licence
     * @param Sectors|null $sectors
     * @param $countrys
     * @param int|null $cabotage
     * @param int|null $declaration
     * @param int|null $emissions
     * @param int|null $permitsRequired
     * @param int|null $trips
     * @param string|null $internationalJourneys
     * @param string|null $dateReceived
     * @return EcmtPermitApplication
     */
    public static function createNew(
        RefData $status,
        RefData $paymentStatus,
        RefData $permitType,
        Licence $licence,
        string $dateReceived = null,
        Sectors $sectors = null,
        $countrys = [],
        int $cabotage = null,
        int $declaration = null,
        int $emissions = null,
        int $permitsRequired = null,
        int $trips = null,
        RefData $internationalJourneys = null
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->status = $status;
        $ecmtPermitApplication->paymentStatus = $paymentStatus; //@todo drop payment status column
        $ecmtPermitApplication->permitType = $permitType;
        $ecmtPermitApplication->licence = $licence;
        $ecmtPermitApplication->sectors = $sectors;
        $ecmtPermitApplication->updateCountrys($countrys);
        $ecmtPermitApplication->cabotage = $cabotage;
        $ecmtPermitApplication->declaration = $declaration;
        $ecmtPermitApplication->emissions = $emissions;
        $ecmtPermitApplication->permitsRequired = $permitsRequired;
        $ecmtPermitApplication->trips = $trips;
        $ecmtPermitApplication->internationalJourneys = $internationalJourneys;
        $ecmtPermitApplication->dateReceived = static::processDate($dateReceived);

        return $ecmtPermitApplication;
    }


    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData $status Status
     * @param RefData $paymentStatus Payment status
     * @param RefData $permitType Permit type
     * @param Licence $licence Licence
     * @param Sectors|null $sectors
     * @param $countrys
     * @param int|null $cabotage
     * @param int|null $declaration
     * @param int|null $emissions
     * @param int|null $permitsRequired
     * @param int|null $trips
     * @param RefData $internationalJourneys
     * @param string|null $dateReceived
     * @return EcmtPermitApplication
     */
    public function update(
        ?RefData $permitType,
        Licence $licence,
        ?Sectors $sectors = null,
        $countrys = null,
        int $cabotage = null,
        int $declaration = null,
        int $emissions = null,
        int $permitsRequired = null,
        int $trips = null,
        RefData $internationalJourneys = null,
        string $dateReceived = null
    ) {
        $this->permitType = $permitType ?? $this->permitType;
        $this->licence = $licence;
        $this->sectors = $sectors;
        $this->updateCountrys($countrys);
        $this->cabotage = $cabotage;
        $this->checkedAnswers = $declaration; //auto updated alongside declaration for internal apps
        $this->declaration = $declaration;
        $this->emissions = $emissions;
        $this->permitsRequired = $permitsRequired;
        $this->trips = $trips;
        $this->internationalJourneys = $internationalJourneys;
        $this->dateReceived = $this->processDate($dateReceived);

        return $this;
    }

    /**
     * Submit the app
     *
     * @param RefData $submitStatus
     *
     * @return void
     * @throws ForbiddenException
     */
    public function submit(RefData $submitStatus)
    {
        if (!$this->canBeSubmitted()) {
            throw new ForbiddenException('This application is not allowed to be submitted');
        }

        $this->status = $submitStatus;
    }

    public function withdraw(RefData $withdrawStatus)
    {
        if (!$this->canBeWithdrawn()) {
            throw new ForbiddenException('This application is not allowed to be withdrawn');
        }

        $this->status = $withdrawStatus;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        $sectionCompletion = $this->getSectionCompletion(self::SECTIONS);

        return [
            'applicationRef' => $this->getApplicationRef(),
            'canBeCancelled' => $this->canBeCancelled(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'canBeWithdrawn' => $this->canBeWithdrawn(),
            'canBeUpdated' => $this->canBeUpdated(),
            'canCheckAnswers' => $this->canCheckAnswers(),
            'hasCheckedAnswers' => $this->hasCheckedAnswers(),
            'canMakeDeclaration' => $this->canMakeDeclaration(),
            'hasMadeDeclaration' => $this->hasMadeDeclaration(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'isUnderConsideration' => $this->isUnderConsideration(),
            'isCancelled' => $this->isCancelled(),
            'isWithdrawn' => $this->isWithdrawn(),
            'isActive' => $this->isActive(),
            'confirmationSectionCompletion' => $this->getSectionCompletion(self::CONFIRMATION_SECTIONS),
            'sectionCompletion' => $sectionCompletion,
        ];
    }

    /**
     * Updates the application to reflect whether or not cabotage will be carried out. A value of true indicates that
     * cabotage will NOT be carried out on the permit
     *
     * @param bool $cabotage
     */
    public function updateCabotage($cabotage)
    {
        $this->cabotage = $cabotage;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to reflect whether or not the permit will be used only by vehicles compliant with
     * Euro 6 standards. A value of true indicates that the permit will only be used by compliant vehicles
     *
     * @param bool $emissions
     */
    public function updateEmissions($emissions)
    {
        $this->emissions = $emissions;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the intended use of the permit in any countries that have imposed limits
     * on the issue of permits for UK hauliers. The $countrys parameter should be an array of Country objects
     *
     * @param array $countrys
     */
    public function updateCountrys(array $countrys)
    {
        $this->countrys = $countrys;
        $this->hasRestrictedCountries = (bool)count($countrys);
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the number of required permits
     *
     * @param int $permitsRequired
     */
    public function updatePermitsRequired($permitsRequired)
    {
        $this->permitsRequired = $permitsRequired;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the number of trips made in the last 12 months using this licence
     *
     * @param int $trips
     */
    public function updateTrips($trips)
    {
        $this->trips = $trips;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate a sector in which the haulier specialises
     *
     * @param RefData $internationalJourneys
     */
    public function updateInternationalJourneys(RefData $internationalJourneys)
    {
        $this->internationalJourneys = $internationalJourneys;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate a sector in which the haulier specialises
     *
     * @param mixed $sectors
     */
    public function updateSectors($sectors)
    {
        $this->sectors = $sectors;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Reset the checked answers and declaration sections to a value representing 'not completed'
     */
    private function resetCheckAnswersAndDeclaration()
    {
        $this->declaration = false;
        $this->checkedAnswers = false;
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
     * This is a custom validator for the countrys field
     * It isn't completely dynamic because it's assumed that
     * this won't be needed in the futuree
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function countrysPopulated($field)
    {
        if ($this->hasRestrictedCountries === false) {
            return true;
        }

        return $this->collectionHasRecord($field);
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

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isNotYetSubmitted() || $this->isUnderConsideration();
    }

    /**
     * @return bool
     */
    public function isNotYetSubmitted()
    {
        return $this->status->getId() === self::STATUS_NOT_YET_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isUnderConsideration()
    {
        return $this->status->getId() === self::STATUS_UNDER_CONSIDERATION;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status->getId() === self::STATUS_CANCELLED;
    }

    /**
     * @return bool
     */
    public function isWithdrawn()
    {
        return $this->status->getId() === self::STATUS_WITHDRAWN;
    }

    /**
     * Whether the permit application can be submitted
     * @todo this currently reruns the section completion checks, should store the value instead for speed
     *
     * @return bool
     */
    public function canBeSubmitted()
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
     * Whether the permit application can be updated
     *
     * @return bool
     */
    public function canBeUpdated()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Whether a declaration can be made
     * @todo currently reruns section checks, these should be stored for speed reasons
     *
     * @return bool
     */
    public function canCheckAnswers()
    {
        $sections = $this->getSectionCompletion(self::SECTIONS);

        return $sections['allCompleted'] && $this->canBeUpdated();
    }

    /**
     * Whether a declaration can be made
     * @todo currently reruns section checks through canCheckAnswers(), these should be stored for speed reasons
     *
     * @return bool
     */
    public function canMakeDeclaration()
    {
        return $this->hasCheckedAnswers() && $this->canCheckAnswers();
    }

    /**
     * have the answers been checked
     *
     * @return bool
     */
    public function hasCheckedAnswers()
    {
        return $this->fieldIsAgreed('checkedAnswers');
    }

    /**
     * have the answers been checked
     *
     * @return bool
     */
    public function hasMadeDeclaration()
    {
        return $this->fieldIsAgreed('declaration');
    }

    /**
     * Whether the permit application can be withdrawn
     *
     * @return bool
     */
    public function canBeWithdrawn()
    {
        if ($this->isUnderConsideration()) {
            return true;
        }

        return false;
    }

    /**
     * Whether the permit application can be cancelled
     *
     * @return bool
     */
    public function canBeCancelled()
    {
        return $this->status->getId() === self::STATUS_NOT_YET_SUBMITTED;
    }

    /**
     * Get the organisation
     *
     * @return OrganisationEntity
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
    }
}
