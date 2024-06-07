<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChain;
use Dvsa\Olcs\Api\Service\Ebsr\EsbrProcessingInterface;

interface EbsrProcessingAwareInterface
{
    /**
     * @param EbsrProcessingChain $ebsrProcessing
     */
    public function setEbsrProcessing(EbsrProcessingChain $ebsrProcessing);

    /**
     * @return EbsrProcessingChain
     */
    public function getEbsrProcessing(): EbsrProcessingChain;
}
