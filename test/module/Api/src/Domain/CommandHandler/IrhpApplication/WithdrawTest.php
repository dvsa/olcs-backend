<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermUnsuccessful;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Withdraw as Sut;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Withdraw as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractWithdrawApplicationHandlerTest;

/**
 * Class WithdrawTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class WithdrawTest extends AbstractWithdrawApplicationHandlerTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityClass = IrhpApplication::class;
    protected $sutClass = Sut::class;
    protected $cmdClass = Cmd::class;
    protected $emails = [
        WithdrawableInterface::WITHDRAWN_REASON_NOTSUCCESS => SendEcmtShortTermUnsuccessful::class,
        WithdrawableInterface::WITHDRAWN_REASON_BY_USER => SendEcmtShortTermUnsuccessful::class,
        WithdrawableInterface::WITHDRAWN_REASON_DECLINED => SendEcmtShortTermUnsuccessful::class,
        WithdrawableInterface::WITHDRAWN_REASON_UNPAID => SendEcmtShortTermUnsuccessful::class
    ];
}
