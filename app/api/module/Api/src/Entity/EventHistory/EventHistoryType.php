<?php

namespace Dvsa\Olcs\Api\Entity\EventHistory;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventHistoryType Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="event_history_type")
 */
class EventHistoryType extends AbstractEventHistoryType
{
    const EVENT_CODE_PASSWORD_RESET = 'PWR';

}
