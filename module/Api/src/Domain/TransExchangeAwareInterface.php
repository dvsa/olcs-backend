<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient as TransExchangeClient;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientInterface;

/**
 * TransExchangeAwareInterface
 */
interface TransExchangeAwareInterface
{
    public function setTransExchange(TransExchangeClientInterface $transExchange);

    /**
     * @return TransExchangeClientInterface
     */
    public function getTransExchange();
}
