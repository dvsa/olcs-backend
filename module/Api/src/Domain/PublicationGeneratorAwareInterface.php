<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;

/**
 * PublicationGeneratorAwareInterface
 */
interface PublicationGeneratorAwareInterface
{
    /**
     * @param PublicationGenerator $service
     */
    public function setPublicationGenerator(PublicationGenerator $service);

    /**
     * @return PublicationGenerator
     */
    public function getPublicationGenerator();
}
