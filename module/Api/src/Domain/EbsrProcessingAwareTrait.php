<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\Ebsr\EbsrProcessingChain;

trait EbsrProcessingAwareTrait
{
    protected EbsrProcessingChain $ebsrProcessing;

    public function setEbsrProcessing(EbsrProcessingChain $ebsrProcessing)
    {
        $this->ebsrProcessing = $ebsrProcessing;
    }

    public function getEbsrProcessing(): EbsrProcessingChain
    {
        return $this->ebsrProcessing;
    }
}