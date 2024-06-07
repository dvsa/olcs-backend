<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Psr\Log\LoggerInterface;

class EbsrProcessingChain
{
    protected array $processors = [];

    private array $files = [];

    public function __construct(private readonly LoggerInterface $logger, EbsrProcessingInterface ...$processors)
    {
        $this->processors = $processors;
    }

    public function process(string $identifier, array $options = []): array
    {
        // processors are called in the order they are added to the chain (see constructor)
        // important to note that the identifier is passed so that it can be modified by the processors
        foreach ($this->processors as $processor) {
            $this->logger->info('Processing file with ' . get_class($processor). ' input: ' . $identifier);
            $identifier = $processor->process($identifier, $options);
            $this->logger->info('File processed with ' . get_class($processor). ' output: ' . $identifier);
            $this->files[$processor->getOutputType()] = $identifier;
        }
        $this->logger->info('processing outputs ' . json_encode($this->files));
        return $this->getFilesCreated();
    }

    public function getFilesCreated(): array
    {
        return $this->files;
    }
}
