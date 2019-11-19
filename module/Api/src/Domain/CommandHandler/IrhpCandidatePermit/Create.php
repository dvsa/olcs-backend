<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Transfer\Command\IrhpCandidatePermit\Create as CreateIrhpCandidatePermitCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Create an IRHP Candidate Permit for APGG
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $extraRepos = ['IrhpPermitRange', 'IrhpPermitApplication'];

    /**
     * Handle command
     *
     * @param CreateIrhpCandidatePermitCmd $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {

        $irhpPermitRange = $this->getRepo('IrhpPermitRange')->fetchById($command->getIrhpPermitRange());
        $irhpPermitApplication = $this->getRepo('IrhpPermitApplication')->fetchById($command->getIrhpPermitApplication());

        /**
         * @var IrhpCandidatePermitEntity $irhpCandidatePermit
         */
        $irhpCandidatePermit = IrhpCandidatePermitEntity::createForApgg(
            $irhpPermitApplication,
            $irhpPermitRange
        );

        $this->getRepo()->save($irhpCandidatePermit);

        $this->result->addId('IrhpCandidatePermit', $irhpCandidatePermit->getId());
        $this->result->addMessage("IRHP Candidate Permit '{$irhpCandidatePermit->getId()}' created");

        return $this->result;
    }
}
