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
    public function withdraw(RefData $withdrawStatus, RefData $withdrawReason): void;
    public function canBeWithdrawn(?RefData $reason): bool;
    public function isWithdrawn(): bool;
    public function getOutstandingFees(): array;
    public function getWithdrawReason();

    const WITHDRAWN_REASON_NOTSUCCESS = 'permits_app_withdraw_notsuccess';
    const WITHDRAWN_REASON_UNPAID = 'permits_app_withdraw_not_paid';
    const WITHDRAWN_REASON_BY_USER = 'permits_app_withdraw_by_user';
    const WITHDRAWN_REASON_DECLINED = 'permits_app_withdraw_declined';

    const ERR_CANT_WITHDRAW = 'Unable to withdraw this application';
}
