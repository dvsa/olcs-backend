<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawal as CommunityLicWithdrawalEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspension as CommunityLicSuspensionEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawalReason as CommunityLicWithdrawalReasonEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspensionReason as CommunityLicSuspensionReasonEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommunityLic\EditSuspension as Cmd;

/**
 * Edit suspension
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class EditSuspension extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = [
        'CommunityLicSuspension',
        'CommunityLicSuspensionReason',
        'CommunityLicWithdrawal',
        'CommunityLicWithdrawalReason',
        'Licence'
    ];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\EditSuspension $command command
     *
     * @return Result
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        return $result;
    }
}
