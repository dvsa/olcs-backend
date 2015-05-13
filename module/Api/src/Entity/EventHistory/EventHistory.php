<?php

namespace Dvsa\Olcs\Api\Entity\EventHistory;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventHistory Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="event_history",
 *    indexes={
 *        @ORM\Index(name="ix_event_history_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_event_history_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_event_history_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_event_history_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_event_history_event_history_type1_idx", columns={"event_history_type_id"}),
 *        @ORM\Index(name="fk_event_history_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_event_history_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_event_history_bus_reg1_idx", columns={"bus_reg_id"})
 *    }
 * )
 */
class EventHistory extends AbstractEventHistory
{

}
