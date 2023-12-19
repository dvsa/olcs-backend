<?php

/**
 * Send Ebsr Refused Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefused;

/**
 * Send Ebsr Refused Email Test
 * @group ebsrEmails
 *
 * @author Craig R <uk@valtech.co.uk>
 */
class SendEbsrRefusedTest extends SendEbsrEmailTestAbstract
{
    protected $template = 'ebsr-refused';
    protected $sutClass = '\Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrRefused';
    protected $cmdClass = SendEbsrRefused::class;
}
