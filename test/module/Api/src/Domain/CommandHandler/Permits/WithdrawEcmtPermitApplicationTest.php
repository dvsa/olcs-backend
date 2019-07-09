<?php

/**
 * Withdraw ECMT Permit Application Test
 *
 * @author Scott Callaway
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAutomaticallyWithdrawn;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\WithdrawEcmtPermitApplication as Sut;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractWithdrawApplicationHandlerTest;

/**
 * Class WithdrawEcmtPermitApplicationTest
 */
class WithdrawEcmtPermitApplicationTest extends AbstractWithdrawApplicationHandlerTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityClass = EcmtPermitApplication::class;
    protected $sutClass = Sut::class;
    protected $cmdClass = Cmd::class;
    protected $emails = [WithdrawableInterface::WITHDRAWN_REASON_UNPAID => SendEcmtAutomaticallyWithdrawn::class];
}
