<?php

/**
 * Update Variation Completion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Update Variation Completion
 *
 * @NOTE If there are future changes to these rules, it might be worth slightly changing how this works, as it is
 * getting a little messy
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateVariationCompletion extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

    const STATUS_UNCHANGED = Application::VARIATION_STATUS_UNCHANGED;
    const STATUS_UPDATED = Application::VARIATION_STATUS_UPDATED;
    const STATUS_REQUIRES_ATTENTION = Application::VARIATION_STATUS_REQUIRES_ATTENTION;

    protected $repoServiceName = 'Application';

    private $requireAttentionMap = [
        'type_of_licence' => [],
        'business_type' => [],
        'business_details' => [
            'business_type'
        ],
        'addresses' => [
            'business_type'
        ],
        'people' => [
            'business_type'
        ],
        'operating_centres' => [],
        'financial_evidence' => [],
        'transport_managers' => [],
        'vehicles' => [],
        'vehicles_psv' => [],
        'vehicles_declarations' => [],
        'discs' => [],
        'community_licences' => [],
        'safety' => [],
        'conditions_undertakings' => [],
        'financial_history' => [
            'people'
        ],
        'convictions_penalties' => [
            'people'
        ],
        //'undertakings' => [] We don't want this as there is bespoke rules around setting this status
    ];

    private $sectionUpdatedCheckMap = [
        'type_of_licence' => 'hasUpdatedTypeOfLicence',
        'business_type' => 'hasUpdatedBusinessType',
        'business_details' => 'hasUpdatedBusinessDetails',
        'addresses' => 'hasUpdatedAddresses',
        'people' => 'hasUpdatedPeople',
        'operating_centres' => 'hasUpdatedOperatingCentres',
        'financial_evidence' => 'hasSavedSection',
        'transport_managers' => 'hasUpdatedTransportManagers',
        'vehicles' => 'hasUpdatedVehicles',
        'vehicles_psv' => 'hasUpdatedVehicles',
        'vehicles_declarations' => 'hasUpdatedVehicleDeclarations',
        'discs' => 'hasSavedSection',
        'community_licences' => 'hasSavedSection',
        'safety' => 'hasUpdatedSafetySection',
        'conditions_undertakings' => 'hasUpdatedConditionsUndertakings',
        'financial_history' => 'hasUpdatedFinancialHistory',
        // @NOTE Not sure if we need this just yet
        //'licence_history' => 'hasUpdatedLicenceHistory',
        'convictions_penalties' => 'hasUpdatedConvictionsPenalties',
        'undertakings' => 'hasUpdatedUndertakings',
        'declarations_internal' => 'hasUpdateDeclarationsInternal',
    ];

    protected $bespokeRulesMap = [
        'type_of_licence' => 'updateRelatedTypeOfLicenceSections',
        'operating_centres' => 'updateRelatedOperatingCentreSections',
        'people' => 'updateRelatedPeopleSections',
        'transport_managers' => 'updateRelatedTmSections',
        'vehicles' => 'updateRelatedVehiclesSections',
        'vehicles_psv' => 'updateRelatedVehiclesSections',
        'discs' => 'updateRelatedDiscSections',
        'community_licences' => 'updateRelatedCommunityLicencesSections'
    ];

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Licence
     */
    private $licence;

    /**
     * @var array
     */
    private $suffixes = [];

    /**
     * @var array
     */
    private $data = [];

    public function handleCommand(CommandInterface $command)
    {
        $section = $command->getSection();
        $this->data = $command->getData();

        $filter = new CamelCaseToUnderscore();
        $section = strtolower($filter->filter($section));

        $this->application = $this->getRepo()->fetchUsingId($command);
        $this->licence = $this->application->getLicence();

        if (!$this->hasSectionChanged($section)) {
            $this->markSectionUnchanged($section);
        } elseif (!$this->isUpdated($section)) {
            $this->markSectionUpdated($section);
            if ($section !== 'undertakings' && $section !== 'declarations_internal') {
                $this->resetUndertakings();
            }
        }

        $this->updateSectionsRequiringAttention($section);

        $this->applyBespokeRules();

        $this->getRepo()->save($this->application);

        $result = new Result();
        $result->addMessage('Updated variation completion status');

        return $result;
    }

    /**
     * Method to call the corresponding business rule
     *
     * @param string $section
     * @return boolean
     */
    private function hasSectionChanged($section)
    {
        return $this->{$this->sectionUpdatedCheckMap[$section]}();
    }

    /**
     * Business rules to check if the TOL section has been updated
     *
     * @return boolean
     */
    protected function hasUpdatedTypeOfLicence()
    {
        return $this->application->getLicenceType() !== $this->licence->getLicenceType();
    }

    protected function hasUpdatedBusinessType()
    {
        return ($this->data['type'] !== $this->licence->getOrganisation()->getType()->getId());
    }

    protected function hasUpdatedBusinessDetails()
    {
        // If requires attention or already updated, then we mark the section as updated
        if (!$this->isUnchanged('business_details')) {
            return true;
        }

        // Otherwise we check for an actual change
        if ($this->data['hasChanged']) {
            return true;
        }

        return false;
    }

    protected function hasUpdatedAddresses()
    {
        // If requires attention or already updated, then we mark the section as updated
        if (!$this->isUnchanged('addresses')) {
            return true;
        }

        // Otherwise we check for an actual change
        if ($this->data['hasChanged']) {
            return true;
        }

        return false;
    }

    protected function hasUpdatedPeople()
    {
        // If requires attention or already updated, then we mark the section as updated
        if (!$this->isUnchanged('people')) {
            return true;
        }

        return !$this->application->getApplicationOrganisationPersons()->isEmpty();
    }

    protected function hasUpdatedSafetySection()
    {
        // Otherwise we check for an actual change
        if ($this->data['hasChanged']) {
            return true;
        }

        return false;
    }

    /**
     * A generic callback that marks a section as complete
     *
     * @return boolean
     */
    protected function hasSavedSection()
    {
        return true;
    }

    /**
     * Business rules to check if the OC section has been updated
     *
     * @return boolean
     */
    protected function hasUpdatedOperatingCentres()
    {
        if (!$this->isUnchanged('operating_centres')) {
            return true;
        }

        return $this->hasActuallyUpdatedOperatingCentres();
    }

    protected function hasActuallyUpdatedOperatingCentres()
    {
        if ($this->application->getOperatingCentres()->count() > 0) {
            return true;
        }

        return $this->application->hasAuthChanged();
    }

    /**
     * If we have updated the transport manager section
     *
     * @return boolean
     */
    protected function hasUpdatedTransportManagers()
    {
        return ($this->application->getTransportManagers()->count() > 0);
    }

    /**
     * If we have updated the vehicles section
     *
     * @return boolean
     */
    protected function hasUpdatedVehicles()
    {
        return ($this->application->getLicenceVehicles()->count() > 0);
    }

    /**
     * If we have updated vehicle declarations
     *
     * @return boolean
     */
    protected function hasUpdatedVehicleDeclarations()
    {
        $fields = [
            'PsvOperateSmallVhl',
            'PsvSmallVhlNotes',
            'PsvSmallVhlConfirmation',
            'PsvNoSmallVhlConfirmation',
            'PsvLimousines',
            'PsvNoLimousineConfirmation',
            'PsvOnlyLimousinesConfirmation'
        ];

        return $this->hasCompletedFields($fields);
    }

    /**
     * If we have updated conditions undertakings
     *
     * @return boolean
     */
    protected function hasUpdatedConditionsUndertakings()
    {
        return ($this->application->getConditionUndertakings()->count() > 0);
    }

    /**
     * If we have updated financial history
     *
     * @return boolean
     */
    protected function hasUpdatedFinancialHistory()
    {
        $fields = [
            'Bankrupt',
            'Administration',
            'Disqualified',
            'Liquidation',
            'Receivership',
            'InsolvencyConfirmation',
            'InsolvencyDetails'
        ];

        return $this->hasCompletedFields($fields);
    }

    /**
     * If we have updated convictions penalties section
     *
     * @return boolean
     */
    protected function hasUpdatedConvictionsPenalties()
    {
        if ($this->application->getConvictionsConfirmation() !== 0) {
            return true;
        }

        if ($this->application->getPrevConviction() !== null) {
            return true;
        }

        return false;
    }

    /**
     * If we have updated undertakings
     *
     * @return boolean
     */
    protected function hasUpdatedUndertakings()
    {
        return ($this->application->getDeclarationConfirmation() === 'Y');
    }

    /**
     *
     * @return bool
     */
    protected function hasUpdateDeclarationsInternal()
    {
        return ($this->application->getAuthSignature());
    }


    /**
     * If we have completed at least 1 of the fields in the list
     *
     * @param array $data
     * @param array $fields
     * @return boolean
     */
    protected function hasCompletedFields($fields)
    {
        foreach ($fields as $field) {
            $value = $this->application->{'get' . $field}();
            if (!empty($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mark a section as required
     *
     * @param string $section
     */
    protected function markSectionRequired($section)
    {
        $this->markSectionStatus($section, self::STATUS_REQUIRES_ATTENTION);
    }

    /**
     * Mark a section as unchanged
     *
     * @param string $section
     */
    protected function markSectionUnchanged($section)
    {
        $this->markSectionStatus($section, self::STATUS_UNCHANGED);
    }

    /**
     * Mark a section as updated
     *
     * @param string $section
     */
    protected function markSectionUpdated($section)
    {
        $this->markSectionStatus($section, self::STATUS_UPDATED);
    }

    /**
     * Mark a section with the given status
     *
     * @param string $section
     * @param int $status
     */
    protected function markSectionStatus($section, $status)
    {
        $setter = 'set' . $this->getSectionAsSuffix($section) . 'Status';

        $this->application->getApplicationCompletion()->$setter($status);
    }

    /**
     * Check if a section has been updated
     *
     * @param string $section
     * @return boolean
     */
    protected function isUpdated($section)
    {
        return $this->isStatus($section, self::STATUS_UPDATED);
    }

    /**
     * Check if the section is unchanged
     *
     * @param string $section
     * @return string
     */
    protected function isUnchanged($section)
    {
        return $this->isStatus($section, self::STATUS_UNCHANGED);
    }

    /**
     * Check if the section requires attention
     *
     * @param string $section
     * @return string
     */
    protected function doesRequireAttention($section)
    {
        return $this->isStatus($section, self::STATUS_REQUIRES_ATTENTION);
    }

    /**
     * Shared logic to check a sections status
     *
     * @param string $section
     * @param int $status
     * @return boolean
     */
    protected function isStatus($section, $status)
    {
        $getter = 'get' . $this->getSectionAsSuffix($section) . 'Status';

        return (int)$this->application->getApplicationCompletion()->$getter() == (int)$status;
    }

    protected function getSectionAsSuffix($section)
    {
        if (empty($this->suffixes[$section])) {
            $filter = new UnderscoreToCamelCase();
            $this->suffixes[$section] = $filter->filter($section);
        }

        return $this->suffixes[$section];
    }

    /**
     * Reset the undertakings section
     */
    protected function resetUndertakings()
    {
        $this->application->setDeclarationConfirmation('N');

        if (!$this->isInternalUser()) {
            $this->markSectionRequired('undertakings');
        }
    }

    /**
     * Apply the generic rules on sections requiring attention
     *
     * @param string $currentSection
     */
    protected function updateSectionsRequiringAttention($currentSection)
    {
        foreach ($this->requireAttentionMap as $section => $triggers) {

            // Skip the current section, or updated sections
            if ($section === $currentSection || $this->isUpdated($section)) {
                continue;
            }

            // Mark each section as unchanged
            $this->markSectionUnchanged($section);

            // Unless the related sections have been updated
            foreach ($triggers as $trigger) {
                if ($this->isUpdated($trigger)) {
                    $this->markSectionRequired($section);
                }
            }
        }
    }

    /**
     * Some sections have more complicated rules, we hook into these here
     */
    protected function applyBespokeRules()
    {
        foreach ($this->bespokeRulesMap as $section => $callback) {
            if ($this->isUpdated($section)) {
                $this->$callback();
            }
        }
    }

    /**
     * Apply bespoke type of licence rules
     */
    protected function updateRelatedTypeOfLicenceSections()
    {
        // If the old licence type was restricted and it is being upgraded
        if ($this->application->isLicenceUpgrade()) {

            $relatedSections = [
                'addresses',
                'financial_evidence',
                'transport_managers',
                'financial_history',
                'convictions_penalties'
            ];

            foreach ($relatedSections as $section) {
                if ($this->isUnchanged($section)) {
                    $this->markSectionRequired($section);
                }
            }
        }

        if ($this->application->isPsvDowngrade() && $this->isUnchanged('operating_centres')) {
            $this->markSectionRequired('operating_centres');
        }
    }

    /**
     * Apply the operating centre rules
     */
    protected function updateRelatedOperatingCentreSections()
    {
        // If the financial evidence section is unchanged (Not requires attention or updated)
        // ...and we have increased the total auth vehicles
        if ($this->isUnchanged('financial_evidence') && $this->hasTotAuthVehiclesIncreased()) {
            $this->markSectionRequired('financial_evidence');
        }

        $vehSection = $this->getRelevantVehicleSection();

        // If the vehicle section is unchanged AND the totAuthVehicles has dropped below the number of vehicles added
        if ($this->isUnchanged($vehSection) && $this->hasTotAuthVehiclesDroppedBelowVehicleCount()) {
            $this->markSectionRequired($vehSection);
        }

        // PSV rules only
        if ($this->isPsv()) {
            // If the discs section is unchanged AND the totAuthVehicles has dropped below the number of discs added
            if ($this->isUnchanged('discs') && $this->hasTotAuthVehiclesDroppedBelowDiscsCount()) {
                $this->markSectionRequired('discs');
            }

            // If the vehicles declaration section is unchanged and any of the tot auth vehicle columns has increased
            if ($this->isUnchanged('vehicles_declarations') && $this->application->hasAuthChanged()) {
                $this->markSectionRequired('vehicles_declarations');
            }
        }

        // If auth hasn't changed, and we are not downgrading
        if (!$this->hasActuallyUpdatedOperatingCentres() && $this->application->isPsvDowngrade() === false) {
            $this->markSectionUnchanged('operating_centres');
        }
    }

    protected function updateRelatedPeopleSections()
    {
        // Don't change if we require attention
        if ($this->doesRequireAttention('people')) {
            return;
        }

        if ($this->application->getApplicationOrganisationPersons()->isEmpty()) {
            $this->markSectionUnchanged('people');
            return;
        }

        $this->markSectionUpdated('people');
    }

    protected function updateRelatedTmSections()
    {
        if (!$this->hasUpdatedTransportManagers()) {
            if ($this->application->isLicenceUpgrade()) {
                $this->markSectionRequired('transport_managers');
            } else {
                $this->markSectionUnchanged('transport_managers');
            }
        }
    }

    protected function updateRelatedVehiclesSections()
    {
        $licenceVehicles = $this->licence->getActiveVehiclesCount();
        $applicationVehicles = $this->application->getActiveVehicles()->count();

        $totalVehicles = $licenceVehicles + $applicationVehicles;

        if ($totalVehicles > (int)$this->application->getTotAuthVehicles()) {
            $this->markSectionRequired($this->getRelevantVehicleSection());
            return;
        }

        if ($applicationVehicles < 1) {
            $this->markSectionUnchanged($this->getRelevantVehicleSection());
            return;
        }

        $this->markSectionUpdated($this->getRelevantVehicleSection());
    }

    protected function updateRelatedDiscSections()
    {
        if ((int)$this->application->getTotAuthVehicles() < (int)$this->licence->getPsvDiscsNotCeasedCount()) {
            $this->markSectionRequired('discs');
            return;
        }

        $this->markSectionUpdated('discs');
    }

    protected function updateRelatedCommunityLicencesSections()
    {
        $activeComLics = (int)$this->licence->getActiveCommunityLicences()->count();
        $totAuth = (int)$this->application->getTotAuthVehicles();

        if ($totAuth < $activeComLics) {
            $this->markSectionRequired('community_licences');
            return;
        }

        $this->markSectionUpdated('community_licences');
    }

    /**
     * Check whether the total auth vehicles has been increased
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesIncreased()
    {
        $totAuthVehicles = $this->getTotAuthVehicles($this->application);
        $totAuthLicenceVehicles = $this->getTotAuthVehicles($this->licence);

        return $totAuthVehicles > $totAuthLicenceVehicles;
    }

    /**
     * Grab the tot vehicle auths for both application and licence
     *
     * @return array
     */
    protected function getTotAuthVehicles($entity)
    {
        if ($this->isPsv()) {
            return $entity->getTotAuthSmallVehicles()
                + $entity->getTotAuthMediumVehicles()
                + $entity->getTotAuthLargeVehicles();
        }

        return $entity->getTotAuthVehicles();
    }

    /**
     * Check whether the licence is psv
     *
     * @return boolean
     */
    protected function isPsv()
    {
        return $this->application->isPsv();
    }

    /**
     * Return the section name of the vehicle section based on whether the licence is goods or psv
     *
     * @return string
     */
    protected function getRelevantVehicleSection()
    {
        if ($this->isPsv()) {
            return 'vehicles_psv';
        }

        return 'vehicles';
    }

    /**
     * Check whether the total auth vehicles has dropped below the number of vehicles added
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesDroppedBelowVehicleCount()
    {
        $totAuthVehicles = $this->getTotAuthVehicles($this->application);

        // @todo Should this just be counting vehicles linked to the licence?
        $totVehicles = $this->countVehicles($this->licence->getLicenceVehicles());

        return $totAuthVehicles < $totVehicles;
    }

    protected function countVehicles($vehicles)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->isNull('removalDate'));

        return $vehicles->matching($criteria)->count();
    }

    /**
     * Check whether the total auth vehicles has dropped below the number of discs added
     *
     * @param array $data
     * @return boolean
     */
    protected function hasTotAuthVehiclesDroppedBelowDiscsCount()
    {
        $totAuthVehicles = $this->getTotAuthVehicles($this->application);
        $totDiscs = $this->licence->getPsvDiscs()->count();

        return $totAuthVehicles < $totDiscs;
    }

    /** Not sure if this is needed yet
    public function hasUpdatedLicenceHistory()
    {
        $data = $this->getVariationCompletionStatusData();

        $fields = [
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenAtPi',
            'prevBeenDisqualifiedTc',
            'prevPurchasedAssets'
        ];

        return $this->hasCompletedFields($data, $fields);
    }*/
}
