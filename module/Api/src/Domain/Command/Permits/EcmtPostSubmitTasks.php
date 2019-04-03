<?php

/**
 * Ecmt Post Submission Tasks
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

final class EcmtPostSubmitTasks extends AbstractCommand
{
    use Identity;
}
