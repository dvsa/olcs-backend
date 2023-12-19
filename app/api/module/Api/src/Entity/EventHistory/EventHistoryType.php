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
    public const EVENT_CODE_PASSWORD_RESET = 'PWR';
    public const EVENT_CODE_SURRENDER_UNDER_CONSIDERATION = 'SUC';
    public const EVENT_CODE_SURRENDER_APPLICATION_WITHDRAWN = 'SAW';
    public const IRHP_APPLICATION_CREATED = 'PAC';
    public const IRHP_APPLICATION_UPDATED = 'PAU';
    public const IRHP_APPLICATION_SUBMITTED = 'PAS';
    public const IRHP_APPLICATION_GRANTED = 'PAG';
    public const USER_EMAIL_ADDRESS_UPDATED = 'UEU';
    public const INTERIM_END = 'INE';
}
