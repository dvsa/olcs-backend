<?php

declare(strict_types=1);

namespace Olcs\Db\Service\Search\Indices;

use Olcs\Db\Service\Search\Indices\Terms\ComplexTermInterface;
use Olcs\Db\Service\Search\Indices\Terms\TransportManagerLicenceStatus;

class Person extends AbstractIndex
{
    /** @return ComplexTermInterface[] */
    public function getFilters(): array
    {
        if (!isset($this->filters)) {
            $this->filters = [
                new TransportManagerLicenceStatus(),
            ];
        }

        return $this->filters;
    }
}
