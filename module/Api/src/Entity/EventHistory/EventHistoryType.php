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
    const EVENT_CODE_SURRENDER_UNDER_CONSIDERATION = 'SUC';
    const EVENT_CODE_SURRENDER_APPLICATION_WITHDRAWN = 'SAW';
    const IRHP_APPLICATION_CREATED = 'PAC';
    const IRHP_APPLICATION_UPDATED = 'PAU';
    const IRHP_APPLICATION_SUBMITTED = 'PAS';
    const IRHP_APPLICATION_GRANTED = 'PAG';
}
