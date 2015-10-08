<?php

namespace Dvsa\Olcs\Api\Domain;
use RandomLib\Generator;

/**
 * Random Aware
 */
trait RandomAwareTrait
{
    /**
     * @var Generator
     */
    protected $randomGenerator;

    /**
     * @return Generator
     */
    public function getRandomGenerator()
    {
        return $this->randomGenerator;
    }

    /**
     * @param Generator $randomGenerator
     */
    public function setRandomGenerator(Generator $randomGenerator)
    {
        $this->randomGenerator = $randomGenerator;
    }
}
