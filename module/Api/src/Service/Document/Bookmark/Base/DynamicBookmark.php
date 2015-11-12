<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark\Base;

use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;

/**
 * Dynamic bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class DynamicBookmark extends AbstractBookmark implements RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;

    const TYPE = 'dynamic';

    protected $data = [];

    public function setData($data)
    {
        $this->data = $data;
    }

    abstract public function getQuery(array $data);
}
