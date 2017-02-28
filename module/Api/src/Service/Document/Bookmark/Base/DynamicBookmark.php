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

    protected $params = [];

    protected $descriptions = [
        'application' => 'application',
        'licence' => 'licence',
        'user' => 'user',
        'publication' => 'publication',
        'publicationId' => 'publication',
        'opposition' => 'opposition',
        'irfoPsvAuth' => 'IRFO PSV auth',
    ];

    public function setData($data)
    {
        $this->data = $data;
    }

    abstract public function getQuery(array $data);

    /**
     * Validate data and get query
     *
     * @param array $data data
     *
     * @return bool
     */
    public function validateDataAndGetQuery($data)
    {
        if (count($this->params) > 0) {
            foreach ($this->params as $param) {
                if (!isset($data[$param])) {
                    $description = isset($this->descriptions[$param]) ? $this->descriptions[$param] : $param;
                    throw new \Exception('no ' . $description . ' data');
                }
            }
        }
        return $this->getQuery($data);
    }
}
