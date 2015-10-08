<?php

namespace Dvsa\Olcs\Api\Domain;
use Dvsa\Olcs\Api\Service\OpenAm\ClientInterface;

/**
 * OpenAmClient Aware Interface
 */
interface OpenAmClientAwareInterface
{
    /**
     * @param ClientInterface $service
     */
    public function setOpenAmClient(ClientInterface $service);

    /**
     * @return ClientInterface
     */
    public function getOpenAmClient();
}
