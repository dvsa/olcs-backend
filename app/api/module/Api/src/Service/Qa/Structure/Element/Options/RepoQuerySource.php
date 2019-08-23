<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;

class RepoQuerySource implements SourceInterface
{
    /** @var RepositoryServiceManager */
    private $repoServiceManager;

    /**
     * Create service instance
     *
     * @param RepositoryServiceManager $repoServiceManager
     *
     * @return RepoQuerySource
     */
    public function __construct(RepositoryServiceManager $repoServiceManager)
    {
        $this->repoServiceManager = $repoServiceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function populateOptionList(OptionList $optionList, array $options)
    {
        $methodName = $options['method'];
        $items = $this->repoServiceManager->get($options['repo'])->$methodName();

        foreach ($items as $item) {
            $hint = null;
            if (isset($item['hint'])) {
                $hint = $item['hint'];
            }

            $optionList->add(
                $item['value'],
                $item['label'],
                $hint
            );
        }
    }
}
