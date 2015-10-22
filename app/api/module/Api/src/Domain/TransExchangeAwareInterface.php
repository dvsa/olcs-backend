<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchange\Client as TransExchangeClient;

/**
 * TransExchangeAwareInterface
 */
interface TransExchangeAwareInterface
{
    /**
     * @param TransExchangeClient $transExchange
     */
    public function setTransExchange(TransExchangeClient $transExchange);

    /**
     * @return TransExchangeClient
     */
    public function getTransExchange();
}
