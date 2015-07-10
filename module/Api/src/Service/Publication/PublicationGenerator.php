<?php

namespace Dvsa\Olcs\Api\Service\Publication;

use Zend\Di\ServiceLocatorInterface;

class PublicationGenerator
{
    private $publicationConfig;
    private $publicationContextManager;
    private $publicationProcessManager;

    public function __construct($config, ServiceLocatorInterface $context, ServiceLocatorInterface $process)
    {
        $this->publicationConfig = $config;
        $this->publicationContextManager = $context;
        $this->publicationProcessManager = $process;

    }

    public function createPublication($publicationConfigKey, $publication, $context)
    {
        if (!isset($this->publicationConfig[$publicationConfigKey])) {
            throw new \Exception('Invalid publication config');
        }

        $contextObject = $this->fetchContext($this->publicationConfig[$publicationConfigKey], $context);

        $this->processPublication($this->publicationConfig[$publicationConfigKey], $publication, $contextObject);
    }

    private function fetchContext($config, $existingContext)
    {
        $context = new \ArrayObject($existingContext);

        if (isset($config['context'])) {
            foreach ($config['context'] as $contextClass) {
                $this->publicationContextManager->get($contextClass)->provide($context);
            }
        }

        $contextArray = $context->getArrayCopy();

        return new ImmutableArrayObject($contextArray);
    }

    private function processPublication($config, $publication, $context)
    {
        if (!isset($config['process'])) {
            throw new \Exception('No publication processors specified');
        }

        foreach ($config['process'] as $process) {
            $this->publicationProcessManager->get($process)->process($publication, $context);
        }
    }
}
