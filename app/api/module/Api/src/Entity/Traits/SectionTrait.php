<?php

namespace Dvsa\Olcs\Api\Entity\Traits;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\SectionableInterface;

/**
 * Section Trait
 */
trait SectionTrait
{
    /**
     * @var array
     */
    private $sectionCompletion;

    /**
     * Is the field agreed
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsAgreed($field)
    {
        return $this->$field === true;
    }

    /**
     * Is the field not null
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsNotNull($field)
    {
        return $this->$field !== null;
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
        return !$this->$field->isEmpty();
    }

    /**
     * Get the section completion
     *
     * @return array
     * @throws RuntimeException
     */
    public function getSectionCompletion()
    {
        if (!isset($this->sectionCompletion)) {
            $this->populateSectionCompletion();
        }
        return $this->sectionCompletion;
    }

    /**
     * Populate the section completion
     *
     * @return void
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function populateSectionCompletion()
    {
        if (!defined('static::SECTIONS')) {
            throw new RuntimeException('Missing required definition of sections');
        }

        $this->sectionCompletion = [];

        $sections = static::SECTIONS;

        foreach ($sections as $field => $section) {
            // validate the field
            $this->validateField($field);
        }

        // populate the summary
        $totalSections = count($sections);

        $countByStatus = array_count_values($this->sectionCompletion);
        $totalCompleted = isset($countByStatus[SectionableInterface::SECTION_COMPLETION_COMPLETED])
            ? $countByStatus[SectionableInterface::SECTION_COMPLETION_COMPLETED] : 0;

        $this->sectionCompletion['totalSections'] = $totalSections;
        $this->sectionCompletion['totalCompleted'] = $totalCompleted;
        $this->sectionCompletion['allCompleted'] = ($totalSections === $totalCompleted);
    }

    /**
     * Validate the field
     *
     * @param string $field field being validated
     *
     * @return void
     */
    private function validateField($field)
    {
        if (isset($this->sectionCompletion[$field])) {
            // already validated
            return;
        }

        $section = static::SECTIONS[$field];

        if (isset($section['validateIf']) && is_array($section['validateIf'])) {
            foreach ($section['validateIf'] as $f => $v) {
                // validate the dependency first
                $this->validateField($f);

                // check if value of the dependency field is what is expected
                if ($this->sectionCompletion[$f] !== $v) {
                    // the first value different to what is expected sets the field to cannot start
                    $this->sectionCompletion[$field] = SectionableInterface::SECTION_COMPLETION_CANNOT_START;
                    return;
                }
            }
        }

        // validate the field itself
        $validator = $section['validator'];

        $this->sectionCompletion[$field] = $this->$validator($field)
            ? SectionableInterface::SECTION_COMPLETION_COMPLETED
            : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
    }

    /**
     * Checks if particular field validates
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    protected function isFieldReadyToComplete($field)
    {
        $this->validateField($field);
        return $this->sectionCompletion[$field] !== SectionableInterface::SECTION_COMPLETION_CANNOT_START;
    }
}
