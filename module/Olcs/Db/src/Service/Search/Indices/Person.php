<?php

declare(strict_types=1);

namespace Olcs\Db\Service\Search\Indices;

use InvalidArgumentException;
use Olcs\Db\Service\Search\Indices\Terms\ComplexTermInterface;
use Olcs\Db\Service\Search\Indices\Terms\TransportManagerLicenceStatus;

class Person
{
    protected array $filters;

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

    /** @throws InvalidArgumentException */
    public function getFilter(string $name): ComplexTermInterface
    {
        $name = preg_replace_callback('/(^|_)([a-z])/', fn($m) => strtoupper($m[2]), $name);

        foreach ($this->getFilters() as $filter) {
            if (str_ends_with($filter::class, $name)) {
                return $filter;
            }
        }

        throw new InvalidArgumentException(sprintf('Filter named %s not found', $name));
    }
}
