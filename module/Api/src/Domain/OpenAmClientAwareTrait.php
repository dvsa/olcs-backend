<?php

namespace Dvsa\Olcs\Api\Domain;
use Dvsa\Olcs\Api\Service\OpenAm\ClientInterface;

/**
 * OpenAmClient Aware
 */
trait OpenAmClientAwareTrait
{
    /**
     * @var ClientInterface
     */
    protected $openAmClient;

    /**
     * @return \Dvsa\Olcs\Api\Service\OpenAm\ClientInterface
     */
    public function getOpenAmClient()
    {
        return $this->openAmClient;
    }

    /**
     * @param \Dvsa\Olcs\Api\Service\OpenAm\ClientInterface $openAmClient
     */
    public function setOpenAmClient(ClientInterface $openAmClient)
    {
        $this->openAmClient = $openAmClient;
    }
}
