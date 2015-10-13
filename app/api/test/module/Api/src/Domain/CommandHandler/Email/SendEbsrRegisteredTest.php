<?php

/**
 * Send Ebsr Registered Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Mockery as m;
use Doctrine\ORM\Query;

/**
 * Send Ebsr Registered Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
class SendEbsrRegisteredTest extends SendEbsrEmailTestAbstract
{
    protected $template = 'ebsr-registered';
    protected $sutClass = '\Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrRegistered';
}
