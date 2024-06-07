<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

interface EbsrProcessingInterface
{
    public function getOutputType(): string;

    public function process(string $identifier, array $options = []): string;
}
