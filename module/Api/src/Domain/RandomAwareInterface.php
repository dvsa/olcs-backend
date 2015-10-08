<?php

namespace Dvsa\Olcs\Api\Domain;
use RandomLib\Generator;

/**
 * Random Aware Interface
 */
interface RandomAwareInterface
{
    /**
     * @param Generator $service
     */
    public function setRandomGenerator(Generator $service);

    /**
     * @return Generator
     */
    public function getRandomGenerator();
}
