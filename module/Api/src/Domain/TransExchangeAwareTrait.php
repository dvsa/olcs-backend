<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchange\Client as TransExchangeClient;

/**
 * TransExchange Aware Trait
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
trait TransExchangeAwareTrait
{
    /**
     * @var TransExchangeClient
     */
    private $transExchange;

    /**
     * @param TransExchangeClient $transExchange
     */
    public function setTransExchange(TransExchangeClient $transExchange)
    {
        $this->transExchange = $transExchange;
    }

    /**
     * @return TransExchangeClient
     */
    public function getTransExchange()
    {
        return $this->transExchange;
    }
}
