<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Domain\Repository\RefData as RefDataRepository;

class RefDataSource implements SourceInterface
{
    /** @var RefDataRepository */
    private $refDataRepo;

    /**
     * Create service instance
     *
     * @param RefDataRepository $refDataRepo
     *
     * @return RefDataSource
     */
    public function __construct(RefDataRepository $refDataRepo)
    {
        $this->refDataRepo = $refDataRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function generateOptions(array $options)
    {
        $refDatas = $this->refDataRepo->fetchByCategoryId($options['categoryId']);

        $options = [];
        foreach ($refDatas as $refData) {
            $options[$refData->getId()] = $refData->getDescription();
        }

        return $options;
    }
}
