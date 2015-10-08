<?php

/**
 * Send Ebsr Received Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Mockery as m;
use Doctrine\ORM\Query;

/**
 * Send Ebsr Received Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
class SendEbsrReceivedTest extends SendEbsrEmailTestAbstract
{
    protected $template = 'ebsr-received';
    protected $sutClass = '\Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrReceived';
}
