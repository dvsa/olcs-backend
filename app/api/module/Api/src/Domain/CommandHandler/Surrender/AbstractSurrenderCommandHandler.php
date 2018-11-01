<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

abstract class AbstractSurrenderCommandHandler extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface, ToggleRequiredInterface
{
    use \Dvsa\Olcs\Api\Domain\AuthAwareTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_SURRENDER];

    protected $repoServiceName = 'Surrender';
}
