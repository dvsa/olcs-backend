<?php

namespace Dvsa\Olcs\Api\Service\Document;

/**
 * Naming Service Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface NamingServiceAwareInterface
{
    /**
     * @param NamingService $service
     */
    public function setNamingService(NamingService $service);

    /**
     * @return NamingService
     */
    public function getNamingService();
}
