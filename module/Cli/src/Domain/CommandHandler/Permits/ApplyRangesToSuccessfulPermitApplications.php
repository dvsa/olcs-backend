<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\ApplyRangesToSuccessfulPermitApplications
    as ApplyRangesToSuccessfulPermitApplicationsCommand;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList;
use RuntimeException;

/**
 * Apply ranges to successful permit applications
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplyRangesToSuccessfulPermitApplications extends ScoringCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    const ENTITY_KEY = 'entity';
    const COUNTRY_IDS_KEY = 'countryIds';
    const PERMITS_REMAINING_KEY = 'permitsRemaining';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpCandidatePermit';

    protected $extraRepos = ['IrhpPermit', 'IrhpPermitRange'];

    /** @var array */
    private $ranges;

    /** @var array */
    private $restrictedCountryIds;

    /**
     * Handle command
     *
     * @param CommandInterface|ApplyRangesToSuccessfulPermitApplicationsCommand $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $stockId = $command->getStockId();

        $this->populateRestrictedCountryIds();

        $this->populateRanges(
            $this->getRepo('IrhpPermitRange')->getByStockId($stockId)
        );

        $candidatePermits = $this->getRepo()->getSuccessfulScoreOrderedInScope($stockId);

        $this->profileMessage('apply ranges to successful permit applications...');

        foreach ($candidatePermits as $candidatePermit) {
            $applicationCountries = $candidatePermit->getIrhpPermitApplication()
                ->getEcmtPermitApplication()
                ->getCountrys()
                ->getValues();

            $message = sprintf(
                '  - processing candidate permit %d with randomised score %f',
                $candidatePermit->getId(),
                $candidatePermit->getRandomizedScore()
            );
            $this->result->addMessage($message);

            $selectedRange = $this->selectRangeForCandidatePermit(
                $this->extractIdsFromEntities($applicationCountries)
            );

            $message = sprintf(
                '    - assigned range id %d to candidate permit %d',
                $selectedRange[self::ENTITY_KEY]->getId(),
                $candidatePermit->getId()
            );
            $this->result->addMessage($message);

            $candidatePermit->applyRange($selectedRange[self::ENTITY_KEY]);
            $this->getRepo()->saveOnFlush($candidatePermit);
            $this->decrementRangeStock($selectedRange);
        }

        $this->profileMessage('flushing apply ranges...');

        $this->getRepo()->flushAll();

        return $this->result;
    }

    /**
     * Returns the best fitting range for an application with the specified countries
     *
     * @param array $applicationCountryIds The country ids specified in the application
     *
     * @return array
     */
    private function selectRangeForCandidatePermit(array $applicationCountryIds)
    {
        if (count($applicationCountryIds) > 0) {
            $this->result->addMessage('    - has one or more countries: ' . implode(', ', $applicationCountryIds));
            return $this->selectRangeForCandidatePermitWithCountries($applicationCountryIds);
        }

        $this->result->addMessage('    - has no countries');
        return $this->selectRangeForCandidatePermitWithNoCountries();
    }

    /**
     * Returns the best fitting range for an application that specifies one or more countries
     *
     * @param array $applicationCountryIds The country ids specified in the application
     *
     * @return array
     */
    private function selectRangeForCandidatePermitWithCountries(array $applicationCountryIds)
    {
        $matchingRanges = $this->getRestrictedRangesWithMostMatchingCountries($applicationCountryIds);

        switch (count($matchingRanges)) {
            case 0:
                return $this->selectRangeForCandidatePermitWithCountriesAndNoMatchingRanges();
            case 1:
                $matchingRange = $matchingRanges[0];

                $message = sprintf(
                    '    - range %d with countries %s has the most matching countries',
                    $matchingRange[self::ENTITY_KEY]->getId(),
                    implode(', ', $matchingRange[self::COUNTRY_IDS_KEY])
                );

                $this->result->addMessage($message);
                return $matchingRange;
        }

        return $this->selectRangeForCandidatePermitWithCountriesAndMultipleMatchingRanges(
            $matchingRanges,
            $applicationCountryIds
        );
    }

    /**
     * Selects the irhp_permit_range best-suited for a candidate permit that has countries
     * but no matching ranges.
     *
     * @throws RuntimeException
     *
     * @return array the irhp_permit_range best suited for the candidate permit
     */
    private function selectRangeForCandidatePermitWithCountriesAndNoMatchingRanges()
    {
        $this->result->addMessage('    - no restricted ranges found with matching countries');

        $matchingRange = $this->getUnrestrictedRangeWithLowestStartNumber();

        if (is_null($matchingRange)) {
            $this->result->addMessage('    - no unrestricted ranges found with lowest start number');

            $ranges = $this->getRestrictedRangesWithFewestCountries();

            if (empty($ranges)) {
                throw new RuntimeException(
                    'Assertion failed in method ' . __METHOD__ . ': count($ranges) == 0'
                );
            }

            if (count($ranges) > 1) {
                throw new RuntimeException(
                    'Assertion failed in method ' . __METHOD__ . ': count($ranges) > 1'
                );
            }

            $matchingRange = $ranges[0]; // Use first range

            $message = sprintf(
                '    - using first restricted range with fewest countries: id %d has countries %s',
                $matchingRange[self::ENTITY_KEY]->getId(),
                implode(', ', $matchingRange[self::COUNTRY_IDS_KEY])
            );
        } else {
            $rangeEntity = $matchingRange[self::ENTITY_KEY];
            $message = sprintf(
                '    - using unrestricted range with lowest start number: id %d starts at %d',
                $rangeEntity->getId(),
                $rangeEntity->getFromNo()
            );
        }

        $this->result->addMessage($message);
        return $matchingRange;
    }

    /**
     * Selects the appropriate irhp_permit_range for a candidate permit with associated countries
     * and multiple matching ranges.
     *
     * @param array $matchingRanges an array of the multiple matching ranges
     * @param array $applicationCountryIds The country ids specified in the application
     *
     * @throws RuntimeException
     *
     * @return array the single range identified as suitable
     */
    private function selectRangeForCandidatePermitWithCountriesAndMultipleMatchingRanges(
        array $matchingRanges,
        array $applicationCountryIds
    ) {
        $this->result->addMessage('    - more than one range found with most matching countries:');
        foreach ($matchingRanges as $matchingRange) {
            $message = sprintf(
                '      - range with id %d and countries %s',
                $matchingRange[self::ENTITY_KEY]->getId(),
                implode(', ', $matchingRange[self::COUNTRY_IDS_KEY])
            );
            $this->result->addMessage($message);
        }

        $matchingRanges = $this->getRangesWithFewestCountriesNotRequestedByApplication(
            $applicationCountryIds,
            $matchingRanges
        );

        if (count($matchingRanges) > 1) {
            throw new RuntimeException(
                'Assertion failed in method ' . __METHOD__ . ': count($matchingRanges) > 1'
            );
        }

        $matchingRange = $matchingRanges[0];

        $this->result->addMessage(
            sprintf(
                '    - range %d with countries %s has the fewest non-requested countries',
                $matchingRange[self::ENTITY_KEY]->getId(),
                implode(', ', $matchingRange[self::COUNTRY_IDS_KEY])
            )
        );

        return $matchingRange;
    }

    /**
     * Returns the best fitting range for an application that specifies no countries
     *
     * @return array
     */
    private function selectRangeForCandidatePermitWithNoCountries()
    {
        $range = $this->getUnrestrictedRangeWithLowestStartNumber();

        if (is_null($range)) {
            $this->result->addMessage('    - no unrestricted ranges available, use restricted range with fewest countries');

            $ranges = $this->getRestrictedRangesWithFewestCountries();
            switch (count($ranges)) {
                case 0:
                    throw new RuntimeException(
                        'Assertion failed in method ' . __METHOD__ . ': count($ranges) == 0'
                    );
                case 1:
                    $range = $ranges[0];
                    break;
                default:
                    throw new RuntimeException(
                        'Assertion failed in method ' . __METHOD__ . ': count($ranges) > 0'
                    );
            }

            $message = sprintf(
                '    - using restricted range with fewest countries: id %d with countries %s',
                $range[self::ENTITY_KEY]->getId(),
                implode(', ', $range[self::COUNTRY_IDS_KEY])
            );
        } else {
            $rangeEntity = $range[self::ENTITY_KEY];

            $message = sprintf(
                '    - using unrestricted range with lowest start number: id %d starts at %d',
                $rangeEntity->getId(),
                $rangeEntity->getFromNo()
            );
        }

        $this->result->addMessage($message);

        return $range;
    }

    /**
     * Returns the set of one or more restricted ranges (i.e. ranges that allow travel to one or more of the
     * restricted countries) that share the lowest number of restricted countries amongst the full set of ranges
     *
     * @throws RuntimeException
     *
     * @return array
     */
    private function getRestrictedRangesWithFewestCountries()
    {
        $fewestCountriesCount = null;
        $restrictedRangesWithFewestCountries = [];

        foreach ($this->getRestrictedRanges() as $range) {
            $rangeCountriesCount = count($range[self::COUNTRY_IDS_KEY]);

            if (is_null($fewestCountriesCount) || ($rangeCountriesCount < $fewestCountriesCount)) {
                $fewestCountriesCount = $rangeCountriesCount;
                $restrictedRangesWithFewestCountries = [$range];
            } elseif ($rangeCountriesCount == $fewestCountriesCount) {
                $restrictedRangesWithFewestCountries[] = $range;
            }
        }

        return $restrictedRangesWithFewestCountries;
    }

    /**
     * Returns the set of restricted ranges (i.e. ranges that allow travel to one or more of the restricted countries)
     *
     * @return array
     */
    private function getRestrictedRanges()
    {
        $restrictedRanges = [];

        foreach ($this->ranges as $range) {
            if (count($range[self::COUNTRY_IDS_KEY]) > 0) {
                $restrictedRanges[] = $range;
            }
        }

        return $restrictedRanges;
    }

    /**
     * From the set of ranges specified in the parameter list, return the ranges that have the fewest restricted
     * countries NOT requested by the application
     *
     * @param array $applicationCountryIds The country ids requested in the application
     * @param array $ranges The ranges to search
     *
     * @return array
     */
    private function getRangesWithFewestCountriesNotRequestedByApplication(
        array $applicationCountryIds,
        array $ranges
    ) {
        $nonRequestedCountryIds = array_diff(
            $this->restrictedCountryIds,
            $applicationCountryIds
        );

        $fewestCommonCountriesCount = null;
        foreach ($ranges as $range) {
            $commonCountries = array_intersect(
                $nonRequestedCountryIds,
                $range[self::COUNTRY_IDS_KEY]
            );

            $commonCountriesCount = count($commonCountries);

            if (is_null($fewestCommonCountriesCount) || ($commonCountriesCount < $fewestCommonCountriesCount)) {
                $fewestCommonCountriesCount = $commonCountriesCount;
                $rangesWithFewestCommonCountries = [$range];
            } elseif ($commonCountriesCount == $fewestCommonCountriesCount) {
                $rangesWithFewestCommonCountries[] = $range;
            }
        }

        return $rangesWithFewestCommonCountries;
    }

    /**
     * From the set of restricted ranges (i.e. ranges that allow travel to one or more restricted countries), return
     * the range that has the most countries in common with those passed in
     *
     * @param array $applicationCountryIds The country ids requested in the application
     *
     * @return array
     */
    private function getRestrictedRangesWithMostMatchingCountries(array $applicationCountryIds)
    {
        $maxCommonCountryCount = 0;
        $matchingRanges = [];

        foreach ($this->getRestrictedRanges() as $range) {
            $commonCountryIds = array_intersect(
                $applicationCountryIds,
                $range[self::COUNTRY_IDS_KEY]
            );
            $commonCountryCount = count($commonCountryIds);

            if ($commonCountryCount > 0) {
                if ($commonCountryCount > $maxCommonCountryCount) {
                    $maxCommonCountryCount = $commonCountryCount;
                    $matchingRanges = [$range];
                } elseif ($commonCountryCount == $maxCommonCountryCount) {
                    $matchingRanges[] = $range;
                }
            }
        }

        return $matchingRanges;
    }

    /**
     * From the set of unrestricted ranges (i.e. ranges that do not allow travel to any of the restricted countries),
     * return the range that has the lowest start number. Returns null if no unrestricted ranges were found
     *
     * @return array|null
     */
    private function getUnrestrictedRangeWithLowestStartNumber()
    {
        $lowestStartNumber = null;
        $unrestrictedRangeWithLowestStartNumber = null;

        foreach ($this->ranges as $range) {
            if (count($range[self::COUNTRY_IDS_KEY]) == 0) {
                $fromNo = $range[self::ENTITY_KEY]->getFromNo();
                if (is_null($lowestStartNumber) || ($fromNo < $lowestStartNumber)) {
                    $lowestStartNumber = $fromNo;
                    $unrestrictedRangeWithLowestStartNumber = $range;
                }
            }
        }

        return $unrestrictedRangeWithLowestStartNumber;
    }

    /**
     * Populate the ranges instance variable with an array, with each element representing one range and consisting of
     * a reference to the range entity, a list of restricted country ids permitted by this range, and the count of free
     * permit numbers remaining within this range
     *
     * @param array $rangeEntities An array of Range entities
     *
     * @throws RuntimeException
     */
    private function populateRanges(array $rangeEntities)
    {
        $this->ranges = [];
        foreach ($rangeEntities as $rangeEntity) {
            $allocatedPermitsInRange = $this->getRepo('IrhpPermit')->getPermitCountByRange($rangeEntity->getId());
            $permitsRemainingInRange = $rangeEntity->getSize() - $allocatedPermitsInRange;

            if ($permitsRemainingInRange > 0) {
                $this->ranges[] = [
                    self::ENTITY_KEY => $rangeEntity,
                    self::COUNTRY_IDS_KEY => $this->extractIdsFromEntities(
                        $rangeEntity->getCountrys()->getValues()
                    ),
                    self::PERMITS_REMAINING_KEY => $permitsRemainingInRange
                ];
            } elseif ($permitsRemainingInRange < 0) {
                throw new RuntimeException(
                    'Assertion failed in method ' . __METHOD__ . ': $permitsRemainingInRange < 0'
                );
            }
        }
    }

    /**
     * Decrement the count of permits available within a given range following a permit being assigned to that range
     *
     * @param array $selectedRange
     */
    private function decrementRangeStock(array $selectedRange)
    {
        $selectedRange = $selectedRange[self::ENTITY_KEY];
        $idToDecrement = $selectedRange->getId();

        $updatedRanges = [];
        foreach ($this->ranges as &$range) {
            $rangeEntity = $range[self::ENTITY_KEY];
            if ($rangeEntity->getId() == $idToDecrement) {
                $range[self::PERMITS_REMAINING_KEY]--;
                $message = sprintf(
                    '    - decrementing stock in range %d, stock is now %d',
                    $idToDecrement,
                    $range[self::PERMITS_REMAINING_KEY]
                );
                $this->result->addMessage($message);
            }
            if ($range[self::PERMITS_REMAINING_KEY] > 0) {
                $updatedRanges[] = $range;
            } else {
                $message = sprintf('    - stock in range %d is now exhausted', $idToDecrement);
                $this->result->addMessage($message);
            }
        }

        $this->ranges = $updatedRanges;
    }

    /**
     * Populate a list of ids representing the restricted countries
     */
    private function populateRestrictedCountryIds()
    {
        $result = $this->handleQuery(
            EcmtConstrainedCountriesList::create(['hasEcmtConstraints' => 1])
        );

        $this->restrictedCountryIds = array_column($result['result'], 'id');
    }

    /**
     * Derive an array of numeric ids by calling getId on an array of entities
     *
     * @param array $entities
     *
     * @return array
     */
    private function extractIdsFromEntities(array $entities)
    {
        $ids = [];

        foreach ($entities as $entity) {
            $ids[] = $entity->getId();
        }

        return $ids;
    }
}
