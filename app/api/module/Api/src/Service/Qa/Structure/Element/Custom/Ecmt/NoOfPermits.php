<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;

class NoOfPermits implements ElementInterface
{
    /** @var int */
    private $maxCanApplyFor;

    /** @var int */
    private $maxPermitted;

    /** @var int */
    private $applicationFee;

    /** @var int */
    private $issueFee;

    /** @var array */
    private $emissionsCategories = [];

    /**
     * Create instance
     *
     * @param int $maxCanApplyFor
     * @param int $maxPermitted
     * @param int $applicationFee
     * @param int $issueFee
     *
     * @return NoOfPermits
     */
    public function __construct($maxCanApplyFor, $maxPermitted, $applicationFee, $issueFee)
    {
        $this->maxCanApplyFor = $maxCanApplyFor;
        $this->maxPermitted = $maxPermitted;
        $this->applicationFee = $applicationFee;
        $this->issueFee = $issueFee;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepresentation()
    {
        return [
            'maxCanApplyFor' => $this->maxCanApplyFor,
            'maxPermitted' => $this->maxPermitted,
            'applicationFee' => $this->applicationFee,
            'issueFee' => $this->issueFee,
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
