<?php

declare(strict_types=1);

namespace Olcs\Db\Service\Search\Indices;

use InvalidArgumentException;
use Olcs\Db\Service\Search\Indices\Terms\ComplexTermInterface;
use Olcs\Db\Service\Search\Indices\Terms\TransportManagerLicenceStatus;

abstract class AbstractIndex
{
    protected array $filters;

    /** @return ComplexTermInterface[] */
    abstract public function getFilters(): array;
}
