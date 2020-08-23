<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Partial;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;

/**
 * Retrieve a partial by id
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class ById extends AbstractQueryByIdHandler
{
    protected $repoServiceName = 'Partial';
    protected $bundle = [
        'partialMarkups' => ['language'],
        'partialCategoryLinks' => ['category', 'subCategory']
    ];
}
