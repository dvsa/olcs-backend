<?php

namespace Dvsa\Olcs\Cli\Domain\Command;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\FeatureToggleConfigName;
use Dvsa\Olcs\Transfer\FieldType\Traits\FeatureToggleFriendlyName;
use Dvsa\Olcs\Transfer\FieldType\Traits\FeatureToggleStatus;

final class UpdateFeatureToggle extends AbstractCommand
{

    use FeatureToggleFriendlyName;
    use FeatureToggleConfigName;
    use FeatureToggleStatus;
}