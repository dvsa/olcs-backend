<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\Ebsr;


use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\ProcessPackException;
use Psr\Log\LoggerInterface;

class EbsrProcessingChain
{
    protected array $processors = [];

    private array $files = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, EbsrProcessingInterface  ...$processors)
    {
        $this->processors = $processors;

        $this->logger = $logger;
    }

    /**
     * @param string $identifier
     * @param array $options
     * @return array
     **/
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

        return $this->getFilesCreated();
    }

    /**
     * @return array
     */
    public function getFilesCreated(): array
    {
        return $this->files;
    }
}
