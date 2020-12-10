<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Withdrawable interface - indicates entity can be withdrawn
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
interface WithdrawableInterface
{
    public function getId();
    public function withdraw(RefData $withdrawStatus, RefData $withdrawReason, $checkReasonAgainstStatus): void;
    public function canBeWithdrawn(?RefData $reason): bool;
    public function isWithdrawn(): bool;
    public function getOutstandingFees(): array;
    public function getWithdrawReason();
    public function getAppWithdrawnEmailCommand($withdrawReason);

    const WITHDRAWN_REASON_NOTSUCCESS = 'permits_app_withdraw_notsuccess';
    const WITHDRAWN_REASON_UNPAID = 'permits_app_withdraw_not_paid';
    const WITHDRAWN_REASON_BY_USER = 'permits_app_withdraw_by_user';
    const WITHDRAWN_REASON_DECLINED = 'permits_app_withdraw_declined';
    const WITHDRAWN_REASON_PERMITS_REVOKED = 'permits_app_withdraw_permits_rev';

    const ERR_CANT_WITHDRAW = 'Unable to withdraw this application';
    const ERR_CANT_DECLINE = 'Unable to decline this application, not in awaiting fee state';
}
