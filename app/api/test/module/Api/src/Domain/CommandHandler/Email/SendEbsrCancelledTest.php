<?php

/**
 * Send Ebsr Cancelled Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrCancelled as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Ebsr\SubmissionCreate as SubmissionCreateCommand;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Service\Client as EmailClient;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Ebsr\EbsrSubmission as EbsrSubmissionQuery;

/**
 * Send Ebsr Cancelled Email Test
 *
 * @author Craig R <uk@valtech.co.uk>
 */
class SendEbsrCancelledTest extends SendEbsrEmailTestAbstract
{
    protected $template = 'ebsr-cancelled';
    protected $sutClass = '\Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEbsrCancelled';
}
