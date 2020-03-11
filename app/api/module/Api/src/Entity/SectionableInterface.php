<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

/**
 * Sectionable Interface
 */
interface SectionableInterface
{
    const SECTION_COMPLETION_CANNOT_START = 'section_sts_csy';
    const SECTION_COMPLETION_NOT_STARTED = 'section_sts_nys';
    const SECTION_COMPLETION_INCOMPLETE = 'section_sts_inc';
    const SECTION_COMPLETION_COMPLETED = 'section_sts_com';

    const VALIDATOR_ALWAYS_FALSE = 'validator_always_false';

    /**
     * Get the section completion
     *
     * @return array
     * @throws RuntimeException
     */
    public function getSectionCompletion();
}
