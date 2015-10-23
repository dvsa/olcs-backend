<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient as TransExchangeClient;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientInterface;

/**
 * TransExchange Aware Trait
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
trait TransExchangeAwareTrait
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientInterface
     */
    private $transExchange;

    /**
     * @param TransExchangeClientInterface $transExchange
     */
    public function setTransExchange(TransExchangeClientInterface $transExchange)
    {
        $this->transExchange = $transExchange;
    }

    /**
     * @return TransExchangeClientInterface
     */
    public function getTransExchange()
    {
        return $this->transExchange;
    }
}
