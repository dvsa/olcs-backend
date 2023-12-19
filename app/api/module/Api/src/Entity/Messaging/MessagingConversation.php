<?php

namespace Dvsa\Olcs\Api\Entity\Messaging;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessagingConversation Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="messaging_conversation",
 *    indexes={
 *        @ORM\Index(name="fk_messaging_conversation_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_messaging_conversation_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_messaging_conversation_task_id", columns={"task_id"})
 *    }
 * )
 */
class MessagingConversation extends AbstractMessagingConversation
{
}
