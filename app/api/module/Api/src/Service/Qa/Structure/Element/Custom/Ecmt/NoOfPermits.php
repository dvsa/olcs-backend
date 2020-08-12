<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class NoOfPermits implements ElementInterface
{
    /** @var int */
    private $year;

    /** @var int */
    private $maxPermitted;

    /** @var array */
    private $emissionsCategories = [];

    /**
     * Create instance
     *
     * @param int $year
     * @param int $maxPermitted
     *
     * @return NoOfPermits
     */
    public function __construct($year, $maxPermitted)
    {
        $this->year = $year;
        $this->maxPermitted = $maxPermitted;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'year' => $this->year,
            'maxPermitted' => $this->maxPermitted,
            'emissionsCategories' => $this->getEmissionsCategoriesRepresentation()
        ];
    }

    /**
     * Get the content of the emissions category key of the representation
     *
     * @return array
     */
    private function getEmissionsCategoriesRepresentation()
    {
        $representation = [];

        foreach ($this->emissionsCategories as $category) {
            $representation[] = $category->getRepresentation();
        }

        return $representation;
    }

    /**
     * Add an emissions category to the representation
     *
     * @param EmissionsCategory $emissionsCategory
     */
    public function addEmissionsCategory(EmissionsCategory $emissionsCategory)
    {
        $this->emissionsCategories[] = $emissionsCategory;
    }
}
