<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DocTemplate;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Retrieve a doc template by id
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class ById extends AbstractQueryByIdHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'DocTemplate';
    protected $bundle = ['document', 'category', 'subCategory' => ['category']];
}
